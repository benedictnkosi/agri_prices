<?php

namespace App\Controller;

use App\Service\PlantingApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class PlantingController extends AbstractController
{
    /**
     * @Route("public/seedling/create", name="createSeedling", methods={"POST"})
     */
    public function createSeedling(Request $request, LoggerInterface $logger, PlantingApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createSeedling($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/seedlings/get", name="getSeedlings", methods={"GET"})
     */
    public function getSeedlings(Request $request, LoggerInterface $logger, PlantingApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getSeedlings($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/transplant/create", name="createTransplant", methods={"POST"})
     */
    public function createTransplant(Request $request, LoggerInterface $logger, PlantingApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createTransplant($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/transplants/get", name="getTransplants", methods={"GET"})
     */
    public function getTransplants(Request $request, LoggerInterface $logger, PlantingApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getTransplants($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }
}
