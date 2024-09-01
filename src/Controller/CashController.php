<?php

namespace App\Controller;

use App\Service\CashApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class CashController extends AbstractController
{
    /**
     * @Route("public/cash/withdraw", name="withdraw", methods={"POST"})
     */
    public function withdraw(Request $request, LoggerInterface $logger, CashApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->withdrawCash($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/cash/report", name="cashReport", methods={"GET"})
     */
    public function cashReport(Request $request, LoggerInterface $logger, CashApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getCashReport($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }
}
