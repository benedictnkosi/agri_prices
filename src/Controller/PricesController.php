<?php

namespace App\Controller;

use App\Service\PricesApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PricesController extends AbstractController
{
    /**
     * @Route("public/prices/get/{market}", name="get_prices_durban", methods={"GET"})
     */
    public function getPrices(string $market, Request $request, LoggerInterface $logger, PricesApi $api): Response{
        $logger->info("Starting Method: " . __METHOD__);
        if (!$request->isMethod('GET')) {
            return new JsonResponse("Method Not Allowed" , 405, array());
        }

        $today = new \DateTime();
        $formattedDate = $today->format('m/d/Y');
        $api->getDurbanPrices($formattedDate);
   
        return new JsonResponse("Done" , 200, array());
    }
}