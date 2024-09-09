<?php

namespace App\Controller;

use App\Service\AgentApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class AgentsController extends AbstractController
{
    /**
     * @Route("public/market/deliver", name="recordMarketDelivery", methods={"POST"})
     */
    public function recordMarketDelivery(Request $request, LoggerInterface $logger, AgentApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->deliverToAgents($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/deliveries/get", name="getMarketDeliveries", methods={"GET"})
     */
    public function getMarketDeliveries(Request $request, LoggerInterface $logger, AgentApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getMarketDeliveries($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/agentsales/record", name="recordAgentSales", methods={"POST"})
     */
    public function recordAgentSales(Request $request, LoggerInterface $logger, AgentApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->recordAgentSale($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/agentsales/get", name="getAgentSales", methods={"GET"})
     */
    public function getAgentSales(Request $request, LoggerInterface $logger, AgentApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getAgentSales($request);
        // $serializer = SerializerBuilder::create()->build();
        // $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($response , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }


}
