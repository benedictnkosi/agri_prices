<?php

namespace App\Service;


use App\Entity\MarketCropsImport;
use App\Entity\Market;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DOMDocument;
use Symfony\Component\HttpFoundation\Request;

class DataApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;

        if (session_id() === '') {
            $logger->info("Session id is empty" . __METHOD__);
            session_start();
        }
    }

    public function singleImport(){
        $this->logger->debug("Starting Method: " . __METHOD__);
        $crop = $this->em->getRepository(MarketCropsImport::class)->findOneBy([], ['lastUpdate' => 'ASC', 'id' => 'DESC']);
        
        $response =  $this->importBulkData($crop->getCropId(), $crop->getCropName(), 90, $crop->getMarket());

        if ($response['result_code'] === 0) {
            $crop->setStatus("Imported. " . $response['number_of_records']);
            $crop->setLastUpdate(new \DateTime());
        }else{
            $crop->setStatus(substr($response['result_message'],0,45)); 
        }

        $this->em->persist($crop);
        $this->em->flush();

        return $response;
    }

    public function importBulkData($productId, $productName, $days, $market): array
    {
        $this->logger->debug("Starting Method: " . __METHOD__);
        try {

            $commodityEntities = [];
            set_time_limit(600);
            ini_set('max_execution_time', '600');

            // Read JSON file
            $jsonFilePath = __DIR__ . '\data\commodities.json';
            if (!file_exists($jsonFilePath)) {
                throw new \Exception("File not found: " . $jsonFilePath);
            }

            $jsonData = file_get_contents($jsonFilePath);
            $commodities = json_decode($jsonData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON decode error: " . json_last_error_msg());
            }

            $this->logger->debug("Commodities: " . print_r($commodities, true));

            // Make the POST request
            $dateObject = new \DateTime();
            $periodName = "Last 90 Days";
            $marketName = "Durban Fresh Produce Market";

            // Construct the URL
            $url = "http://webapps.daff.gov.za/amis/MarketPricesAJAX.jsp?period=$days&market=$market&sid=0.1397918394592692&product=$productId&variety=&class=00&size=&container=00&period-name=Last%207%20Days&market-name=Durban%20Fresh%20Produce%20Market%20(DUR)&product-name=cabbage&variety-name=All%20Varieties&class-name=All%20Classes&size-name=All%20Sizes&container-name=All%20Packages";

            $this->logger->debug("URL: " . $url);

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Set the headers
            $headers = [
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
                "Accept-Language: en-US,en;q=0.9",
                "Connection: keep-alive",
                "Cookie: JSESSIONID=b353b631d4090012facd3ae43b92",
                "Upgrade-Insecure-Requests: 1",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36"
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Execute cURL session
            $response = curl_exec($ch);

                 // Close cURL session
            curl_close($ch);


            // Check for cURL errors
            if ($response === false) {
                $error = curl_error($ch);
                $this->logger->error("cURL error: $error");
                return array(
                    'result_message' => "Server is down",
                    'result_code' => 1
                );
            }


            // Load the HTML into a DOMDocument object
            $dom = new \DOMDocument();
            try {
                libxml_use_internal_errors(true); // Suppress warnings for malformed HTML
                @$dom->loadHTML($response, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();
            } catch (Exception $ex) {
                $this->logger->error("Error loading HTML: " . $ex->getMessage());
                return array(
                    'result_message' => $ex->getMessage(),
                    'result_code' => 1
                );
            }
            
            $numberOfRecords = 0;

            // Get the table by tag name
            $tables = $dom->getElementsByTagName('table');

            foreach ($tables as $table) {

                $rows = $table->getElementsByTagName('tr');
                $this->logger->debug("Cells: " . $rows->length);
                foreach ($rows as $row) {

                    $cells = $row->getElementsByTagName('td');
                    $this->logger->debug("Cells: " . $cells->length);
                    if ($cells->length > 0) {

                        $dateString = $cells->item(0)->nodeValue;
                        $dateFormat = 'l, d F Y'; // Format: Monday, 19 August 2024

                        // Create a DateTime object from the date string
                        $date = \DateTime::createFromFormat($dateFormat, $dateString);

                        // Check if the date is valid
                        if ($date && $date->format($dateFormat) === $dateString) {
                            $dateObject = $date;
                            continue;
                        }

                        if ($cells->length < 13) {
                            $this->logger->debug("This is not a price row");
                            continue;
                        }

                        $unitsSold = intval(trim($cells->item(11)->nodeValue));

                        // Only persist if TotalQuantitySold is not zero
                        if ($unitsSold != 0) {
                            $unitWeight = intval(str_replace(" Kg", "", trim($cells->item(5)->nodeValue)));
                            $kgSold = $unitWeight  * $unitsSold;
                            $commodity = new Market();
                            $commodity->setCommodity($productName);
                            $commodity->setWeight($unitWeight);
                            $commodity->setGrade(trim($cells->item(2)->nodeValue));
                            $commodity->setContainer(trim($cells->item(4)->nodeValue));
                            $commodity->setLowPrice(str_replace("R", "", trim($cells->item(8)->nodeValue)));
                            $commodity->setHighPrice(str_replace("R", "", trim($cells->item(7)->nodeValue)));
                            $commodity->setAveragePrice(str_replace("R", "", trim($cells->item(9)->nodeValue)));
                            $commodity->setSalesTotal(str_replace("R", "", trim($cells->item(10)->nodeValue)));
                            $commodity->setTotalQuantitySold($unitsSold);
                            $commodity->setTotalKgSold($kgSold);
                            $this->logger->debug("Date: " . $dateObject->format('Y-m-d'));
                            $commodity->setDate($dateObject); // Convert to DateTime
                            $commodity->setMarket($marketName);
                            // Check if array with same id exists
                            $existingEntity = null;
                            $id = $cells->item(7)->nodeValue . $cells->item(8)->nodeValue . $cells->item(9)->nodeValue;
                            foreach ($commodityEntities as $entity) {
                                if ($entity['id'] === $id) {
                                    $existingEntity = $entity;
                                    break;
                                }
                            }

                            if ($existingEntity) {
                                // Update existing entity
                                $this->logger->debug("commodity already added " . $cells->item(0)->nodeValue);
                            } else {
                                // Add new entity to commodityEntities
                                $commodityEntities[] = array(
                                    'commodity' => $commodity,
                                    'id' => $id
                                );
                                $this->em->persist($commodity); // Persist each entity
                                $this->logger->debug("added commodity to persist " . $cells->item(0)->nodeValue);
                                $numberOfRecords++;
                            }
                        }
                    }
                }
            }

            // Flush the entity manager to save data to the database
            $this->logger->debug("Done writing to database");
            // }
            $this->em->flush();
            return array(
                'result_message' => "Success",
                'result_code' => 0,
                'number_of_records' => $numberOfRecords
            );
        } catch (Exception $ex) {
            $this->logger->error("Error " . $ex->getMessage());
            return array(
                'result_message' => $ex->getMessage(),
                'result_code' => 1
            );
        }
    }
}
