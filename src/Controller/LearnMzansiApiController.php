<?php

namespace App\Controller;

use App\Service\LearnMzansiApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

class LearnMzansiApiController extends AbstractController
{
    private $api;
    private $logger;

    public function __construct(LearnMzansiApi $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    /**
     * @Route("public/learn/learner/create", name="createLearner", methods={"POST"})
     */
    public function createLearner(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->createLearner($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/learner", name="getLearner", methods={"GET"})
     */
    public function getLearner(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->getLearner($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/grades", name="getGrades", methods={"GET"})
     */
    public function getGrades(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->getGrades($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/questions", name="getQuestionById", methods={"GET"})
     */
    public function getQuestionById(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->getQuestionById($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/question/create", name="createQuestion", methods={"POST"})
     */
    public function createQuestion(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $data = json_decode($request->getContent(), true);
        $response = $this->api->createQuestion($data);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/question/random", name="getRandomQuestionBySubjectId", methods={"GET"})
     */
    public function getRandomQuestionBySubjectId(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $subjectId = $request->query->get('subject_id');
        $uid = $request->query->get('uid');
        $response = $this->api->getRandomQuestionBySubjectId($subjectId, $uid);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/learner/update", name="updateLearner", methods={"POST"})
     */
    public function updateLearner(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->updateLearner($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/learner/subjects", name="getLearnerSubjects", methods={"GET"})
     */
    public function getLearnerSubjects(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->getLearnerSubjects($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/learner/assign-subject", name="assignSubjectToLearner", methods={"POST"})
     */
    public function assignSubjectToLearner(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->assignSubjectToLearner($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/learner/subjects-not-enrolled", name="getSubjectsNotEnrolledByLearner", methods={"GET"})
     */
    public function getSubjectsNotEnrolledByLearner(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->getSubjectsNotEnrolledByLearner($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }

    /**
     * @Route("public/learn/learner/check-answer", name="checkLearnerAnswer", methods={"POST"})
     */
    public function checkLearnerAnswer(Request $request): JsonResponse
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $response = $this->api->checkLearnerAnswer($request);
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($response, 'json');
        return new JsonResponse($jsonContent, 200, array('Access-Control-Allow-Origin' => '*'), true);
    }
}