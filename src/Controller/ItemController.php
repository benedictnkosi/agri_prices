<?php

namespace App\Controller;

use App\Service\ItemApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class ItemController extends AbstractController
{
    

    /**
     * @Route("public/item/delete", name="deleteItem", methods={"POST"})
     */
    public function deleteItem(Request $request, LoggerInterface $logger, ItemApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->deleteItem($request);
        return new JsonResponse($response , 200, array('Access-Control-Allow-Origin' => '*'));
    }
}
