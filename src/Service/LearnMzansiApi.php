<?php

namespace App\Service;

use App\Entity\Grade;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Learner;
use App\Entity\Learnersubjects;
use App\Entity\Question;
use App\Entity\Result;
use App\Entity\Subject;
use App\Entity\Issue;
use phpDocumentor\Reflection\Types\Boolean;

class LearnMzansiApi extends AbstractController
{
    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createLearner(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $uid = $requestBody['uid'];

            if (empty($uid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                $learner = new Learner();
                $learner->setUid($uid);
                $this->em->persist($learner);
                $this->em->flush();

                return array(
                    'status' => 'OK',
                    'message' => 'Successfully created learner'
                );
            } else {
                if ($learner->getGrade()) {
                    return array(
                        'status' => 'NOK',
                        'message' => "Learner already exists $uid",
                        'grade' => $learner->getGrade()->getNumber()
                    );
                } else {
                    return array(
                        'status' => 'NOK',
                        'message' => "Learner already exists $uid",
                        'grade' => "Not assigned"
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating learner'
            );
        }
    }

    public function getLearner(Request $request)
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $uid = $request->query->get('uid');

            if (empty($uid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'UID values are required'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            return $learner;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting learner'
            );
        }
    }

    /**
     * Get question for the ID.
     *
     * @return array
     */
    public function getQuestionById(Request $request): array
    {

        $id = $request->query->get('id');

        if (empty($id)) {
            return array(
                'status' => 'NOK',
                'message' => 'Question id is required'
            );
        }

        $query = $this->em->createQuery(
            'SELECT q
            FROM App\Entity\Question q
            WHERE q.id = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }


    /**
     * Create a new question from JSON request data.
     *
     * @param array $data The JSON request body as an associative array.
     * @return Question|null The created question or null on failure.
     */
    public function createQuestion(array $data)
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $questionId = $data['question_id'] ?? null;

            // Validate required fields
            if (empty($data['question']) || empty($data['type']) || empty($data['subject']) || empty($data['year']) || empty($data['term']) || empty($data['answer'])) {
                return array(
                    'status' => 'NOK',
                    'message' => "Missing required fields."
                );
            }

            //check that the expected answer is not too long
            //spit answer by |
            $answers = explode('|', $data['answer']);
            foreach ($answers as $answer) {
                $numberOfWords = str_word_count($answer);
                if ($numberOfWords > 4 && $data['type'] == 'single') {
                    return array(
                        'status' => 'NOK',
                        'message' => "Too many words in the expected answer, use multiple choice instead."
                    );
                }
            }

            // Fetch the associated Subject entity
            $subject = $this->em->getRepository(Subject::class)->findOneBy(['name' => $data['subject']]);
            if (!$subject) {
                return array(
                    'status' => 'NOK',
                    'message' => "Subject with ID {$data['subject']} not found."
                );
            }

            // Check if a question with the same subject and question text already exists
            $existingQuestion = $this->em->getRepository(Question::class)->findOneBy([
                'subject' => $subject,
                'question' => $data['question']
            ]);

            if ($existingQuestion && $existingQuestion->getId() != $questionId) {

                return array(
                    'status' => 'NOK',
                    'message' => 'A question with the same subject and text already exists. ' . $questionId . ' ' . $existingQuestion->getId()
                );
            }

            $this->logger->info("Creating new question with data: " . json_encode($data));

            // Create a new Question entity

            if ($questionId !== 0) {
                $question = $this->em->getRepository(Question::class)->find($questionId);
                if (!$question) {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Question not found'
                    );
                }
            } else {
                $question = new Question();
            }

            $this->logger->info("debug 1");

            $data['options']['option1'] = str_replace('{"answers":"', '', $data['options']['option1']);
            $data['options']['option1'] = rtrim($data['options']['option1'], '"}');

            $data['options']['option2'] = str_replace('{"answers":"', '', $data['options']['option2']);
            $data['options']['option2'] = rtrim($data['options']['option2'], '"}');


            $data['options']['option3'] = str_replace('{"answers":"', '', $data['options']['option3']);
            $data['options']['option3'] = rtrim($data['options']['option3'], '"}');


            $data['options']['option4'] = str_replace('{"answers":"', '', $data['options']['option4']);
            $data['options']['option4'] = rtrim($data['options']['option4'], '"}');



            $question->setQuestion($data['question']);
            $question->setType($data['type']);
            $question->setSubject($subject);
            $question->setContext($data['context'] ?? null);
            $question->setAnswer(is_array($data['answer']) ? json_encode($data['answer']) : json_encode([$data['answer']]));
            $question->setOptions($data['options'] ?? null); // Pass the array directly
            $question->setTerm($data['term'] ?? null);
            $question->setExplanation($data['explanation'] ?? null);
            $question->setYear($data['year'] ?? null);
            $question->setCapturer($data['capturer'] ?? null);
            $question->setReviewer($data['capturer'] ?? null);

            $this->logger->info("debug 2");
            // Persist and flush the new entity
            $this->em->persist($question);
            $this->em->flush();

            $this->logger->info("debug 3");

            $this->logger->info("Created new question with ID {$question->getId()}.");
            return array(
                'status' => 'OK',
                'message' => 'Successfully created question',
                'question_id' => $question->getId()
            );
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            // Log the error or handle as needed
            error_log($e->getMessage());
            return null;
        }
    }


    function cleanOptions($options)
    {
        $cleanedOptions = [];
        foreach ($options as $key => $value) {
            // Remove the unwanted string
            $value = str_replace(['{\"answers\":\"', '\"}'], '', $value);
            $value = str_replace(['\"}'], '', $value);

            // Trim any leading or trailing whitespace
            $value = trim($value);
            $cleanedOptions[$key] = $value;
        }
        return $cleanedOptions;
    }

    public function getRandomQuestionBySubjectId(int $subjectId, string $uid, int $questionId)
    {
        $this->logger->info("Starting Method: " . __METHOD__);

        try {
            $currentMonth = (int)date('m');
            $termCondition = '';
            $statusCondition = '';

            if ($questionId !== 0) {
                $query = $this->em->createQuery(
                    'SELECT q
                    FROM App\Entity\Question q
                    WHERE q.id = :id'
                )->setParameter('id', $questionId);

                $question = $query->getOneOrNullResult();
                if ($question) {
                    return $question;
                } else {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Question not found'
                    );
                }
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }


            $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'subject' => $subjectId]);

            if ($currentMonth < 7 && !$learner->isOverideterm()) {
                $termCondition = 'AND q.term = 2 ';
            }

            if ($learner->getName() == 'admin') {
                $statusCondition = ' AND q.status = \'new\' ';
            } else {
                $statusCondition = ' AND q.status = \'approved\' ';
            }

            if (!$learnerSubject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner subject not found'
                );
            }



            $query = $this->em->createQuery(
                'SELECT q
            FROM App\Entity\Question q
            JOIN q.subject s
            LEFT JOIN App\Entity\Result r WITH r.question = q AND r.learner = :learner
            WHERE s.id = :subjectId AND r.id IS NULL
            AND q.active = 1 ' . $termCondition . $statusCondition
            )->setParameters([
                'subjectId' => $subjectId,
                'learner' => $learner
            ]);

            $questions = $query->getResult();
            if (!empty($questions)) {
                shuffle($questions);
                $randomQuestion = $questions[0]; // Get the first random question
            } else {
                return array(
                    'status' => 'NOK',
                    'message' => 'No more questions available',
                    'context' => '',
                    'image_path' => ''
                );
            }

            return $randomQuestion;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function updateLearner(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $uid = $requestBody['uid'];
            $name = $requestBody['name'] ?? null;
            $gradeName = $requestBody['grade'] ?? null;

            $this->logger->info("UID: $uid, Name: $name, Grade: $gradeName");

            if (empty($uid) || empty($name) || empty($gradeName)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $gradeName = str_replace('Grade ', '', $gradeName);
            $grade = $this->em->getRepository(Grade::class)->findOneBy(['number' => $gradeName]);
            $this->logger->info("1");
            if (!$grade) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Grade not found'
                );
            }

            $learner->setName($name);
            $learner->setGrade($grade);
            $this->em->persist($learner);
            $this->em->flush();

            //remove all learner subject and results
            $learnerSubjects = $this->em->getRepository(Learnersubjects::class)->findBy(['learner' => $learner]);
            foreach ($learnerSubjects as $learnerSubject) {
                $this->em->remove($learnerSubject);
            }
            $this->em->flush();

            $results = $this->em->getRepository(Result::class)->findBy(['learner' => $learner]);
            foreach ($results as $result) {
                $this->em->remove($result);
            }
            $this->em->flush();


            return array(
                'status' => 'OK',
                'message' => 'Successfully updated learner'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error updating learner'
            );
        }
    }

    public function getGrades(): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $grades = $this->em->getRepository(Grade::class)->findBy(['active' => true]);
            return array(
                'status' => 'OK',
                'grades' => $grades
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting grades'
            );
        }
    }

    public function getLearnerSubjects(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $uid = $request->query->get('uid');

            //test
            if (empty($uid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'UID values are required'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $learnerSubjects = $this->em->getRepository(Learnersubjects::class)->findBy(['learner' => $learner], ['lastUpdated' => 'DESC']);

            if (empty($learnerSubjects)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'No subjects found for learner'
                );
            }
            $answeredQuestions = 0;

            $returnArray = array();
            foreach ($learnerSubjects as $learnerSubject) {
                $query = $this->em->createQueryBuilder()
                    ->select('r, q')
                    ->from('App\Entity\Result', 'r')
                    ->join('r.question', 'q')
                    ->where('r.learner = :learner')
                    ->andWhere('q.subject = :subject')
                    ->setParameter('learner', $learner)
                    ->setParameter('subject', $learnerSubject->getSubject())
                    ->getQuery();


                $results = $query->getResult();
                $answeredQuestions += count($results);

                $totalSubjectQuestion = $this->em->getRepository(Question::class)->createQueryBuilder('q')
                    ->select('count(q.id)')
                    ->where('q.subject = :subject')
                    ->andWhere('q.status = \'approved\'')
                    ->setParameter('subject', $learnerSubject->getSubject())
                    ->getQuery()
                    ->getSingleScalarResult();

                $returnArray[] = array(
                    'subject' => $learnerSubject,
                    'total_questions' => $totalSubjectQuestion,
                    'answered_questions' => $answeredQuestions
                );
            }

            return $returnArray;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting learner subjects'
            );
        }
    }

    public function assignSubjectToLearner(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $uid = $requestBody['uid'];
            $subjectId = $requestBody['subject_id'];

            if (empty($uid) || empty($subjectId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $subject = $this->em->getRepository(Subject::class)->find($subjectId);
            if (!$subject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Subject not found'
                );
            }

            $existingLearnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy([
                'learner' => $learner,
                'subject' => $subject
            ]);

            if ($existingLearnerSubject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Subject already assigned to learner'
                );
            }

            $learnerSubject = new Learnersubjects();
            $learnerSubject->setLearner($learner);
            $learnerSubject->setSubject($subject);
            $learnerSubject->setLastUpdated(new \DateTime());
            $learnerSubject->setPercentage(0);
            $this->em->persist($learnerSubject);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully assigned subject to learner'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error assigning subject to learner'
            );
        }
    }

    public function getSubjectsNotEnrolledByLearner(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $uid = $request->query->get('uid');

            if (empty($uid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'UID values are required'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $enrolledSubjects = $this->em->getRepository(Learnersubjects::class)->findBy(['learner' => $learner]);
            $enrolledSubjectIds = array_map(function ($learnerSubject) {
                return $learnerSubject->getSubject()->getId();
            }, $enrolledSubjects);

            if (empty($enrolledSubjectIds)) {
                $queryBuilder = $this->em->createQueryBuilder();
                $queryBuilder->select('s')
                    ->from('App\Entity\Subject', 's')
                    ->where('s.active = 1')
                    ->andWhere('s.grade = :grade')
                    ->setParameter('grade', $learner->getGrade())
                    ->orderBy('s.name');

                $query = $queryBuilder->getQuery();
            } else {
                $queryBuilder = $this->em->createQueryBuilder();
                $query = $queryBuilder->select('s')
                    ->from('App\Entity\Subject', 's')
                    ->where('s.id NOT IN (:enrolledSubjectIds)')
                    ->andWhere('s.active = 1')
                    ->andWhere('s.grade = :grade')
                    ->setParameter('enrolledSubjectIds', $enrolledSubjectIds)
                    ->setParameter('grade', $learner->getGrade())
                    ->orderBy('s.name')
                    ->getQuery();
            }

            $subjects = $query->getResult();

            if (empty($subjects)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'No subjects found for grade'
                );
            }

            return array(
                'status' => 'OK',
                'subjects' => $subjects
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting subjects not enrolled by learner'
            );
        }
    }

    function normalizeString($string)
    {
        // Replace different types of hyphens and minus signs with a standard hyphen
        $string = str_replace(['−', '–', '—', '―', '−'], '-', $string);
        // Remove any leading or trailing whitespace
        return trim($string);
    }

    public function checkLearnerAnswer(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $questionId = $requestBody['question_id'];
            $learnerAnswers = trim($requestBody['answer']);
            $multiLearnerAnswers = $requestBody['answers'];
            $RequestType = $requestBody['requesting_type'];

            $learnerAnswers = str_replace(' ', '', $learnerAnswers);
            if (is_array($multiLearnerAnswers)) {
                $multiLearnerAnswers = array_map(function ($answer) {
                    return str_replace(' ', '', $answer);
                }, $multiLearnerAnswers);
            }

            $uid = $requestBody['uid'];

            if (empty($questionId) || empty($uid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $question = $this->em->getRepository(Question::class)->find($questionId);
            if (!$question) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question not found'
                );
            }

            if ($question->getType() == 'multi_select') {
                if (empty($multiLearnerAnswers)) {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Mandatory values missing - ' . $question->getType()
                    );
                }
                $multiLearnerAnswers = array_map('trim', $multiLearnerAnswers);
                $learnerAnswers = $multiLearnerAnswers;
            } else {
                if (empty($learnerAnswers)) {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Mandatory values missing - ' . $question->getType()
                    );
                }
            }

            if (!is_array($learnerAnswers)) {
                $learnerAnswers = [$learnerAnswers];
            }

            $correctAnswers = json_decode($question->getAnswer(), true);
            if (!is_array($correctAnswers)) {
                throw new \Exception('Invalid correct answers format');
            }

            $correctAnswers = array_map(function ($answer) {
                return str_replace(' ', '', $answer);
            }, $correctAnswers);

            $correctAnswers = array_map(function ($answer) {
                return $this->normalizeString(str_replace(' ', '', $answer));
            }, $correctAnswers);

            $isCorrect = !array_udiff($learnerAnswers, $correctAnswers, function ($a, $b) {
                $bParts = explode('|', $b);
                foreach ($bParts as $part) {
                    if (strcasecmp($this->normalizeString(urldecode($a)), $this->normalizeString(urldecode($part))) === 0) {
                        $this->logger->info("a: $a, b: $part");
                        return 0;
                    } else {
                        $this->logger->info("a: $a, b: $b");
                    }
                }
                return 1;
            });

            $outcome = $isCorrect ? 'correct' : 'incorrect';

            // Save the result in the Result entity
            if ($RequestType !== 'mock') {
                $result = new Result();
                $result->setLearner($learner);
                $result->setQuestion($question);
                $result->setOutcome($outcome);
                $this->em->persist($result);
                $this->em->flush();

                $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'subject' => $question->getSubject()]);
                if (!$learnerSubject) {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Learner subject not found ' . $question->getSubject()->getId() . ' ' . $learner->getId()
                    );
                }
                $learnerSubject->setLastUpdated(new \DateTime());
                $this->em->persist($learnerSubject);
                $this->em->flush();
            }

            return array(
                'status' => 'OK',
                'is_correct' => $isCorrect,
                'correct_answers' => implode(', ', json_decode($question->getAnswer(), true))
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error checking learner answer'
            );
        }
    }



    public function removeLearnerResultsBySubject(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $uid = $requestBody['uid'];
            $subjectId = $requestBody['subject_id'];

            if (empty($uid) || empty($subjectId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $subject = $this->em->getRepository(Subject::class)->find($subjectId);
            if (!$subject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Subject not found'
                );
            }

            $results = $this->em->getRepository(Result::class)->createQueryBuilder('r')
                ->join('r.question', 'q')
                ->where('r.learner = :learner')
                ->andWhere('q.subject = :subject')
                ->setParameter('learner', $learner)
                ->setParameter('subject', $subject)
                ->getQuery()
                ->getResult();

            foreach ($results as $result) {
                $this->em->remove($result);
            }
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully removed learner results for the subject'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error removing learner results'
            );
        }
    }

    public function getLearnerSubjectPercentage(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $uid = $request->query->get('uid');
            $subjectId = $request->query->get('subject_id');

            if (empty($uid) || empty($subjectId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'UID and Subject ID are required'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $subject = $this->em->getRepository(Subject::class)->find($subjectId);
            if (!$subject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Subject not found'
                );
            }

            $results = $this->em->getRepository(Result::class)->createQueryBuilder('r')
                ->join('r.question', 'q')
                ->where('r.learner = :learner')
                ->andWhere('q.subject = :subject')
                ->setParameter('learner', $learner)
                ->setParameter('subject', $subject)
                ->getQuery()
                ->getResult();

            $totalQuestions = count($results);
            $correctAnswers = 0;

            foreach ($results as $result) {
                if ($result->getOutcome() === 'correct') {
                    $correctAnswers++;
                }
            }


            $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'subject' => $subject]);

            if (!$learnerSubject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner subject not found'
                );
            }

            if (empty($results)) {

                $learnerSubject->setPercentage(0);
                $this->em->persist($learnerSubject);
                $this->em->flush();

                return array(
                    'status' => 'OK',
                    'percentage' => 0
                );
            }


            $percentage = ($correctAnswers / $totalQuestions);
            $learnerSubject->setPercentage($percentage);
            $this->em->persist($learnerSubject);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'learner_subject' => $learnerSubject,
                'percentage' => $percentage
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error calculating learner subject percentage'
            );
        }
    }

    public function setOverrideTerm(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $uid = $requestBody['uid'];
            $override = $requestBody['override'];

            if (empty($uid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }


            $learner->setOverideTerm($override);
            $this->em->persist($learner);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully set override term'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error setting override term'
            );
        }
    }

    public function setHigherGradeFlag(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $uid = $requestBody['uid'];
            $learnerSubjectId = $requestBody['learner_subject_id'];
            $higherGrade = $requestBody['higher_grade'];

            if (empty($uid) || empty($learnerSubjectId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'id' => $learnerSubjectId]);
            if (!$learnerSubject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner subject not found'
                );
            }

            $learnerSubject->setHigherGrade($higherGrade);
            $this->em->persist($learnerSubject);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully set higher grade flag'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error setting higher grade flag'
            );
        }
    }

    public function getAllActiveSubjects($request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $gradeNumber = $request->query->get('grade');
            if (empty($gradeNumber)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Grade is required'
                );
            }

            $grade = $this->em->getRepository(Grade::class)->findOneBy(['number' => $gradeNumber]);
            if (!$grade) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Grade not found'
                );
            }

            $subjects = $this->em->getRepository(Subject::class)->findBy(['active' => true, 'grade' => $grade]);
            return array(
                'status' => 'OK',
                'subjects' => $subjects
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting active subjects'
            );
        }
    }

    public function uploadImage(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $file = $request->files->get('image');
            $questionId = $request->request->get('question_id');
            $imageType = $request->request->get('image_type');

            if (!$file) {
                return array(
                    'status' => 'NOK',
                    'message' => 'No image file provided'
                );
            }

            $question = $this->em->getRepository(Question::class)->find($questionId);
            if (!$question) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question not found'
                );
            }

            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/assets/images/learnMzansi';
            $newFilename = uniqid() . '.' . $file->guessExtension();

            $file->move($uploadDir, $newFilename);

            if ($imageType == 'question') {
                $question->setImagePath($newFilename);
            } else {
                $question->setAnswerImage($newFilename);
            }
            $this->em->persist($question);
            $this->em->flush();


            return array(
                'status' => 'OK',
                'message' => 'Image successfully uploaded',
                'fileName' => $newFilename
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error uploading image'
            );
        }
    }

    public function setImagePathForQuestion(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $questionId = $requestBody['question_id'];
            $imageName = $requestBody['image_name'];

            if (empty($questionId) || empty($imageName)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $question = $this->em->getRepository(Question::class)->find($questionId);
            if (!$question) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question not found'
                );
            }

            $question->setImagePath($imageName);
            $this->em->persist($question);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully set image path for question'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error setting image path for question'
            );
        }
    }

    public function setImageForQuestionAnswer(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $questionId = $requestBody['question_id'];
            $imageName = $requestBody['image_name'];

            if (empty($questionId) || empty($imageName)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $question = $this->em->getRepository(Question::class)->find($questionId);
            if (!$question) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question not found'
                );
            }

            $question->setAnswerImage($imageName);
            $this->em->persist($question);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully set image path for question'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error setting image path for question'
            );
        }
    }

    public function getQuestionsByGradeAndSubject(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $gradeNumber = $request->query->get('grade');
            $subjectName = $request->query->get('subject');

            if (empty($gradeNumber) || empty($subjectName)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Grade and Subject are required'
                );
            }

            $grade = $this->em->getRepository(Grade::class)->findOneBy(['number' => $gradeNumber]);
            if (!$grade) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Grade not found'
                );
            }

            $subject = $this->em->getRepository(Subject::class)->findOneBy(['name' => $subjectName, 'grade' => $grade]);
            if (!$subject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Subject not found'
                );
            }

            $questions = $this->em->getRepository(Question::class)->findBy(['subject' => $subject]);

            return array(
                'status' => 'OK',
                'questions' => $questions
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting questions'
            );
        }
    }


    public function setQuestionInactive(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $questionId = $requestBody['question_id'];

            if (empty($questionId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question ID is required'
                );
            }

            $question = $this->em->getRepository(Question::class)->find($questionId);
            if (!$question) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question not found'
                );
            }

            $question->setActive(0);
            $this->em->persist($question);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully set question to inactive'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error setting question to inactive'
            );
        }
    }

    public function setQuestionStatus(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $questionId = $requestBody['question_id'];
            $status = $requestBody['status'];
            $reviewerEmail = $requestBody['email'];

            if (empty($questionId) || empty($status) || empty($reviewerEmail)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'fields are required'
                );
            }

            $question = $this->em->getRepository(Question::class)->find($questionId);
            if (!$question) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question not found'
                );
            }

            $question->setStatus($status);
            $question->setReviewer($reviewerEmail);
            $this->em->persist($question);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully set question to inactive'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error setting question to inactive'
            );
        }
    }

    public function removeSubjectFromLearner(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $uid = $requestBody['uid'];
            $subjectId = $requestBody['subject_id'];

            if (empty($uid) || empty($subjectId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Mandatory values missing'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $subject = $this->em->getRepository(Subject::class)->find($subjectId);
            if (!$subject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Subject not found'
                );
            }

            $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'subject' => $subject]);
            if (!$learnerSubject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner subject not found'
                );
            }

            // Remove all results for the learner and subject
            $results = $this->em->getRepository(Result::class)->createQueryBuilder('r')
                ->join('r.question', 'q')
                ->where('r.learner = :learner')
                ->andWhere('q.subject = :subject')
                ->setParameter('learner', $learner)
                ->setParameter('subject', $subject)
                ->getQuery()
                ->getResult();

            foreach ($results as $result) {
                $this->em->remove($result);
            }

            // Remove the learner subject
            $this->em->remove($learnerSubject);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Successfully removed subject from learner and deleted related results'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error removing subject from learner'
            );
        }
    }

    public function logIssue(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $comment = $requestBody['comment'];
            $uid = $requestBody['uid'];
            $questionId = $requestBody['question_id'];

            if (empty($comment)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'comment is required'
                );
            }

            $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
            if (!$learner) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner not found'
                );
            }

            $question = $this->em->getRepository(Question::class)->find($questionId);
            if (!$question) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Question not found'
                );
            }

            // Create a new Issue entity
            $issue = new Issue();
            $issue->setComment($comment);
            $issue->setLearner($learner);
            $issue->setCreated(new \DateTime());
            $issue->setQuestion($question);

            // Persist and flush the new entity
            $this->em->persist($issue);
            $this->em->flush();

            $this->logger->info("Logged new issue with ID {$issue->getId()}.");
            return array(
                'status' => 'OK',
                'message' => 'Successfully logged issue',
                'issue_id' => $issue->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error logging issue'
            );
        }
    }

    public function getAllActiveIssues(): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $issues = $this->em->getRepository(Issue::class)->findBy(['status' => 'new']);
            return array(
                'status' => 'OK',
                'issues' => $issues
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting active issues'
            );
        }
    }
}
