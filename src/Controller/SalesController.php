<?php

namespace App\Controller;

use App\Service\SalesApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class SalesController extends AbstractController
{
    /**
     * @Route("public/sale/record", name="recordSale", methods={"POST"})
     */
    public function recordSale(Request $request, LoggerInterface $logger, SalesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->recordSale($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/sales/get", name="getSales", methods={"GET"})
     */
    public function getSales(Request $request, LoggerInterface $logger, SalesApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getSales($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

}
