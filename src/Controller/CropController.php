<?php

namespace App\Controller;

use App\Service\CropApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class CropController extends AbstractController
{
    /**
     * @Route("public/crop/create", name="createCrop", methods={"POST"})
     */
    public function createCrop(Request $request, LoggerInterface $logger, CropApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createCrop($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/crops/get", name="getCrop", methods={"GET"})
     */
    public function getCrops(Request $request, LoggerInterface $logger, CropApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getCrops($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

}
