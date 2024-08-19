<?php

namespace App\Service;

use App\Entity\DurbanMarket;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DOMDocument;
use Symfony\Component\HttpFoundation\Request;

class PricesApi extends AbstractController
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


    public function getDurbanPrices($date): array
    {
        $this->logger->debug("Starting Method: " . __METHOD__);
        try {
            // Make the POST request
            $url = 'https://durbanmarkets.durban.gov.za/';

            // Form data to be sent in the POST request
            $data = array(
                'descItem' => '',
                'massItem' => '',
                'datepickers' => $date
            );

            // Use cURL to send the POST request
            $ch = curl_init($url);

            // Convert the form data array into a URL-encoded string
            $postData = http_build_query($data);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            // Disable SSL verification (not recommended for production)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            // Execute the request and store the response
            $response = curl_exec($ch);

            // Check for cURL errors
            if ($response === false) {
                $error = curl_error($ch);
                echo "cURL error: $error";
            } else {
                // $this->logger->debug("Response: $response");
            }

            // Load the HTML into a DOMDocument object
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true); // Suppress warnings for malformed HTML
            @$dom->loadHTML($response, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            // Get the table by tag name
            $tables = $dom->getElementsByTagName('table');

            foreach ($tables as $table) {

                $rows = $table->getElementsByTagName('tr');
                foreach ($rows as $row) {

                    $cells = $row->getElementsByTagName('td');

                    if ($cells->length > 0) {
                        $totalQuantitySold = (int)trim($cells->item(9)->nodeValue);

                        // Only persist if TotalQuantitySold is not zero
                        if ($totalQuantitySold != 0) {
                            // $this->logger->debug("Cells: " . $cells->item(0)->nodeValue);
                            $commodity = new DurbanMarket();
                            $commodity->setCommodity($cells->item(0)->nodeValue);
                            $commodity->setWeight($cells->item(1)->nodeValue);
                            $commodity->setSizeGrade($cells->item(2)->nodeValue);
                            $commodity->setContainer($cells->item(3)->nodeValue);
                            $commodity->setProvince($cells->item(4)->nodeValue);
                            $commodity->setLowPrice($cells->item(5)->nodeValue);
                            $commodity->setHighPrice($cells->item(6)->nodeValue);
                            $commodity->setAveragePrice($cells->item(7)->nodeValue);
                            $commodity->setSalesTotal($cells->item(8)->nodeValue);
                            $commodity->setTotalQuantitySold($cells->item(9)->nodeValue);
                            $commodity->setTotalKgSold($cells->item(10)->nodeValue);
                            $commodity->setStockOnHand($cells->item(11)->nodeValue);
                            $commodity->setDate(\DateTime::createFromFormat('d/M/Y', trim($cells->item(12)->nodeValue))); // Convert to DateTime
                            $this->em->persist($commodity);  // Persist each entity
                        }
                    }
                }
            }


            // Flush the entity manager to save data to the database
            $this->em->flush();


            // Close the cURL session
            curl_close($ch);


            // Close the cURL session
            curl_close($ch);

            return $this->em->getRepository(DurbanMarket::class)->findAll();
        } catch (Exception $ex) {
            $this->logger->error("Error " . print_r($ex, true));
            return array(
                'result_message' => $ex->getMessage(),
                'result_code' => 1
            );
        }
    }
}
