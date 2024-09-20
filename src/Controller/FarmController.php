<?php

namespace App\Controller;

use App\Service\FarmApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class FarmController extends AbstractController
{
    /**
     * @Route("public/farm/create", name="createFarm", methods={"POST"})
     */
    public function createFarm(Request $request, LoggerInterface $logger, FarmApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createFarm($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/farm/join", name="joinFarm", methods={"POST"})
     */
    public function joinFarm(Request $request, LoggerInterface $logger, FarmApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->JoinFarm($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/farm/target", name="updateTarget", methods={"PUT"})
     */
    public function updateTarget(Request $request, LoggerInterface $logger, FarmApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->updateTarget($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/farm", name="getFarm", methods={"GET"})
     */
    public function getFarm(Request $request, LoggerInterface $logger, FarmApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getFarm($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

}
