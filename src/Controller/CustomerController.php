<?php

namespace App\Controller;

use App\Service\CustomerApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class CustomerController extends AbstractController
{
    /**
     * @Route("public/customer/create", name="createCustomer", methods={"POST"})
     */
    public function createCustomer(Request $request, LoggerInterface $logger, CustomerApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createCustomer($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/customers/get", name="getCustomers", methods={"GET"})
     */
    public function getCustomers(Request $request, LoggerInterface $logger, CustomerApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getCustomers($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

}
