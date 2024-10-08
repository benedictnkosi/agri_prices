<?php

namespace App\Controller;

use App\Service\DashboardApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class DashboardController extends AbstractController
{


    /**
     * @Route("public/dashboard/dailysales", name="getSalesAmountPerDay", methods={"GET"})
     */
    public function getSalesAmountPerDay(Request $request, LoggerInterface $logger, DashboardApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getSalesAmountPerDay($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/dashboard/agentdailysales", name="getAgentSalesAmountPerDay", methods={"GET"})
     */
    public function getAgentSalesAmountPerDay(Request $request, LoggerInterface $logger, DashboardApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getAgentSalesAmountPerDay($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/dashboard/salesStats", name="getSalesStats", methods={"GET"})
     */
    public function getSalesStats(Request $request, LoggerInterface $logger, DashboardApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getSalesStats($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/dashboard/weeklyseedlings", name="getWeeklySeedlings", methods={"GET"})
     */
    public function getWeeklySeedlings(Request $request, LoggerInterface $logger, DashboardApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getWeeklySeedlings($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/dashboard/weeklytransplant", name="getWeeklyTransplants", methods={"GET"})
     */
    public function getWeeklyTransplants(Request $request, LoggerInterface $logger, DashboardApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getWeeklyTransplants($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

}
