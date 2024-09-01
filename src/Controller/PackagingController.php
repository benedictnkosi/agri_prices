<?php

namespace App\Controller;

use App\Service\PackagingApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class PackagingController extends AbstractController
{
    /**
     * @Route("public/packaging/create", name="createPackaging", methods={"POST"})
     */
    public function createPackaging(Request $request, LoggerInterface $logger, PackagingApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createPackaging($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/packaging/get", name="getPackaging", methods={"GET"})
     */
    public function getPackaging(Request $request, LoggerInterface $logger, PackagingApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getPackaging($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }
}
