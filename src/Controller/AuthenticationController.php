<?php

namespace App\Controller;

use App\Service\AuthenticationApi;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("public/users/create", name="createNewUser", methods={"POST"})
     */
    public function createNewUser(Request $request, LoggerInterface $logger, AuthenticationApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->createNewUserIfNotExists($request);
        return new JsonResponse($response, 200, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * @Route("public/users/{uid}", name="getUserByUid", methods={"GET"})
     */
    public function getUserByUid($uid, Request $request, LoggerInterface $logger, AuthenticationApi $api): Response
    {
        $logger->info("Starting Method: " . __METHOD__);
        $response = $api->getUserByUid($uid);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent , 200, array('Access-Control-Allow-Origin' => '*'), true);
    }
}
