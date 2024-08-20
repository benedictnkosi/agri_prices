<?php

namespace App\Service;

use App\Entity\DurbanMarket;
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

    public function getCropPrices(string $crop, string $grade, string $weight, string $period)
    {

        $date = $this->getDate($period);
        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder();
        $qb->select('c')
            ->from(DurbanMarket::class, 'c')
            ->where('c.date >= :monthsAgo')
            ->setParameter('monthsAgo', $date);

        if ($crop !== null) {
            $qb->andWhere('c.commodity LIKE :crop')
                ->setParameter('crop', '%' . $crop . '%');
        }

        if ($grade !== null) {
            $qb->andWhere('c.grade LIKE :grade')
                ->setParameter('grade', $grade);
        }

        if ($weight !== null) {
            $qb->andWhere('c.weight LIKE :weight')
                ->setParameter('weight', $weight);
        }



        return $qb->getQuery()->getResult();
    }

    public function getFiltersForCrop(Request $request)
    {
        $date = $this->getDate($request->query->get('period'));
        $qb = $this->em->createQueryBuilder();
        $field = $request->query->get('field');
        $qb->select("c.$field")
            ->from(DurbanMarket::class, 'c')
            ->where('c.date >= :monthsAgo')
            ->setParameter('monthsAgo', $date)
            ->andWhere('c.commodity LIKE :crop')
            ->setParameter('crop', '%' . $request->query->get('crop') . '%');


        if ($field !== "grade" && !empty($request->query->get('grade'))) {
            $qb->andWhere('c.grade LIKE :grade')
                ->setParameter('grade', $request->query->get('grade'));
        }

        if ($field !== "weight" && !empty($request->query->get('weight'))) {
            $qb->andWhere('c.weight LIKE :weight')
                ->setParameter('weight', $request->query->get('weight'));
        }

        $qb->groupBy("c.$field")
        ->orderBy("c.$field", 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getTotalsByProvince(Request $request)
    {
        $date = $this->getDate($request->query->get('period'));
        $qb = $this->em->createQueryBuilder();
        $qb->select('c.province, SUM(c.salesTotal) as totalSales') // Select province and sum of salesTotal
            ->from(DurbanMarket::class, 'c')
            ->where('c.date >= :monthsAgo')
            ->setParameter('monthsAgo', $date)
            ->andWhere('c.commodity LIKE :crop')
            ->setParameter('crop', '%' . $request->query->get('crop') . '%');

        if (!empty($request->query->get('grade'))) {
            $qb->andWhere('c.grade LIKE :grade')
                ->setParameter('grade', $request->query->get('grade'));
        }

        if (!empty($request->query->get('weight'))) {
            $qb->andWhere('c.weight LIKE :weight')
                ->setParameter('weight', $request->query->get('weight'));
        }

        $qb->groupBy('c.province')
        ->orderBy('totalSales', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function isSalesUp(Request $request)
    {

        $monthsAgo = (int)$request->query->get('period');
        $crop = $request->query->get('crop');
        $currentStartDate = (new \DateTime())->modify("-$monthsAgo months");
        $previousStartDate = (new \DateTime())->modify("-" . ($monthsAgo * 2) . " months");

        $qb = $this->em->createQueryBuilder();
        $qb->select('SUM(c.totalKgSold) as currentPeriodSales, SUM(p.totalKgSold) as previousPeriodSales')
            ->from(DurbanMarket::class, 'c')
            ->leftJoin(DurbanMarket::class, 'p', 'WITH', 'p.date >= :previousStartDate AND p.date < :currentStartDate')
            ->where('c.date >= :currentStartDate')
            ->andWhere('c.commodity LIKE :commodity')
            ->setParameter('currentStartDate', $currentStartDate->format('Y-m-d'))
            ->setParameter('previousStartDate', $previousStartDate->format('Y-m-d'))
            ->setParameter('commodity', '%' . $crop . '%');

        $results = $qb->getQuery()->getSingleResult();


        // Calculate the difference and determine if sales are up or down
        $results['difference'] = $results['currentPeriodSales'] - $results['previousPeriodSales'];
        $results['trend'] = $results['difference'] >= 0 ? 'up' : 'down';

        return $results;
    }

    private function getDate(string $period)
    {
        $monthsAgo = new \DateTime();
        $monthsAgo->modify("-$period month");
        return $monthsAgo;
    }
}
