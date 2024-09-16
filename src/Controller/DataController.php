<?php

namespace App\Controller;

use App\Service\DataApi;
use App\Service\PricesApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class DataController extends AbstractController
{
    
    /**
     * @Route("public/prices/singleinport", name="getSingleImport", methods={"GET"})
     */
    public function getSingleImport(Request $request, LoggerInterface $logger, DataApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
       $response =  $api->singleImport();
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/prices/get/{market}", name="get_prices_durban", methods={"GET"})
     */
    public function importPrices(string $market, Request $request, LoggerInterface $logger, PricesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed", 405, array('Access-Control-Allow-Origin' => '*'));
        }


        // $startDate = new \DateTime('2024-08-08');
        // $endDate = new \DateTime('2024-08-31'); // Current date

        // // Create an interval of 1 day
        // $interval = new \DateInterval('P1D');
        // $dateRange = new \DatePeriod($startDate, $interval, $endDate->add($interval));

        // foreach ($dateRange as $date) {
        //     $formattedDate = $date->format('m/d/Y');
        //     $logger->info("Starting Method: " . $formattedDate);
        //     $api->getDurbanPrices($formattedDate);
        // }

        $date = new \DateTime();
        $formattedDate = $date->format('m/d/Y');
        $api->getDurbanPrices($formattedDate);
        return new JsonResponse("Done", 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/daff/import", name="importDaffPrices", methods={"GET"})
     */
    public function importDaffPrices(Request $request, LoggerInterface $logger, DataApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed", 405, array('Access-Control-Allow-Origin' => '*'));
        }


        // $startDate = new \DateTime('2024-08-08');
        // $endDate = new \DateTime('2024-08-31'); // Current date

        // // Create an interval of 1 day
        // $interval = new \DateInterval('P1D');
        // $dateRange = new \DatePeriod($startDate, $interval, $endDate->add($interval));

        // foreach ($dateRange as $date) {
        //     $formattedDate = $date->format('m/d/Y');
        //     $logger->info("Starting Method: " . $formattedDate);
        //     $api->getDurbanPrices($formattedDate);
        // }

        $api->importBulkData($request->get('productId'), $request->get('productName'),  $request->get('days'));
        return new JsonResponse("Done", 200, array('Access-Control-Allow-Origin' => '*'));
    }
}