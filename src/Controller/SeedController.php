<?php

namespace App\Controller;

use App\Service\SeedApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class SeedController extends AbstractController
{
    /**
     * @Route("public/seeds/create", name="createSeeds", methods={"POST"})
     */
    public function createSeeds(Request $request, LoggerInterface $logger, SeedApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createSeed($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/seeds/get", name="getSeeds", methods={"GET"})
     */
    public function getSeeds(Request $request, LoggerInterface $logger, SeedApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getSeeds($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }
}
