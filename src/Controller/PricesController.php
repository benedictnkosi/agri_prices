<?php

namespace App\Controller;

use App\Service\PricesApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class PricesController extends AbstractController
{
    /**
     * @Route("public/prices/get/{market}", name="get_prices_durban", methods={"GET"})
     */
    public function importPrices(string $market, Request $request, LoggerInterface $logger, PricesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed", 405, array('Access-Control-Allow-Origin' => '*'));
        }


        $startDate = new \DateTime('2024-09-08');
        $endDate = new \DateTime('2024-10-29'); // Current date

        // Create an interval of 1 day
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate->add($interval));

        foreach ($dateRange as $date) {
            $formattedDate = $date->format('m/d/Y');
            $logger->info("Starting Method: " . $formattedDate);
            $api->getDurbanPrices($formattedDate);
        }

        $date = new \DateTime();
        $formattedDate = $date->format('m/d/Y');
        $api->getDurbanPrices($formattedDate);
        return new JsonResponse("Done", 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/prices", name="getPricesByFilters", methods={"GET"})
     */
    public function getPricesByFilters(Request $request, LoggerInterface $logger, PricesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed", 405, array('Access-Control-Allow-Origin' => '*'));
        }

        $crop = $request->query->get('crop');
        $grade = $request->query->get('grade');
        $size = $request->query->get('weight');
        $period = $request->query->get('period');


        // Validate that all parameters are present
        if (empty($crop) || !$request->query->has('grade') || !$request->query->has('weight') || empty($period)) {
            return new JsonResponse("Bad Request: Missing required parameters", 400, array('Access-Control-Allow-Origin' => '*'));
        }

        $response = $api->getCropPrices($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/filters", name="getCropFilters", methods={"GET"})
     */
    public function getCropFilters(Request $request, LoggerInterface $logger, PricesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed", 405, array('Access-Control-Allow-Origin' => '*'));
        }

        $crop = $request->query->get('crop');
        $period = $request->query->get('period');


        // Validate that all parameters are present
        if (empty($crop) || !$request->query->has('grade') || !$request->query->has('weight') || empty($period)) {
            return new JsonResponse("Bad Request: Missing required parameters", 400, array('Access-Control-Allow-Origin' => '*'));
        }

        $response = $api->getFiltersForCrop($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/sales", name="getSalesTotalsByProvince", methods={"GET"})
     */
    public function getSalesTotalsByProvince(Request $request, LoggerInterface $logger, PricesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed", 405, array('Access-Control-Allow-Origin' => '*'));
        }

        $crop = $request->query->get('crop');
        $period = $request->query->get('period');


        // Validate that all parameters are present
        if (empty($crop) || !$request->query->has('grade') || !$request->query->has('weight') || empty($period)) {
            return new JsonResponse("Bad Request: Missing required parameters", 400, array('Access-Control-Allow-Origin' => '*'));
        }

        $response = $api->getTotalsByProvince($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/previousperiodsales", name="previousperiodsales", methods={"GET"})
     */
    public function previousperiodsales(Request $request, LoggerInterface $logger, PricesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed", 405, array('Access-Control-Allow-Origin' => '*'));
        }

        $crop = $request->query->get('crop');
        $period = $request->query->get('period');


        // Validate that all parameters are present
        if (empty($crop) || empty($period)) {
            return new JsonResponse("Bad Request: Missing required parameters", 400, array('Access-Control-Allow-Origin' => '*'));
        }

        $response = $api->getPreviouSalesTotal($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }
}
