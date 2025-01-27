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
            // Validate required fields
            if (empty($data['question']) || empty($data['type']) || empty($data['subject']) || empty($data['year']) || empty($data['term']) || empty($data['answer']) ) {
                return array(
                    'status' => 'NOK',
                    'message' => "Missing required fields."
                );
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

            if ($existingQuestion) {
                return array(
                    'status' => 'NOK',
                    'message' => 'A question with the same subject and text already exists.'
                );
            }

            $this->logger->info("Creating new question with data: " . json_encode($data));

            // Create a new Question entity
            $question = new Question();
            $question->setQuestion($data['question']);
            $question->setType($data['type']);
            $question->setSubject($subject);
            $question->setContext($data['context'] ?? null);
            $question->setAnswer(is_array($data['answer']) ? json_encode($data['answer']) : json_encode([$data['answer']]));
            $question->setOptions($data['options'] ?? null); // Pass the array directly
            $question->setTerm($data['term'] ?? null);
            $question->setImagePath($data['image_path'] ?? null);
            $question->setExplanation($data['explanation'] ?? null);
            $question->setYear($data['year'] ?? null);

            // Persist and flush the new entity
            $this->em->persist($question);
            $this->em->flush();

            $this->logger->info("Created new question with ID {$question->getId()}.");
            return array(
                'status' => 'OK',
                'message' => 'Successfully created question',
                'question_id' => $question->getId()
            );
        } catch (\Exception $e) {
            // Log the error or handle as needed
            error_log($e->getMessage());
            return null;
        }
    }

    public function getRandomQuestionBySubjectId(int $subjectId, string $uid)
    {
        $this->logger->info("Starting Method: " . __METHOD__);

        $learner = $this->em->getRepository(Learner::class)->findOneBy(['uid' => $uid]);
        $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'subject' => $subjectId]);
        try {
            $currentMonth = (int)date('m');
            $termCondition = '';

            if ($currentMonth < 7 && !$learnerSubject->isOverideterm()) {
                $termCondition = 'AND q.term = 2';
            }

            $query = $this->em->createQuery(
                'SELECT q
            FROM App\Entity\Question q
            JOIN q.subject s
            LEFT JOIN App\Entity\Result r WITH r.question = q AND r.learner = :learner
            WHERE s.id = :subjectId AND r.id IS NULL
            AND q.active = 1 ' . $termCondition
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
            $this->logger->info("0");
            //get grade entity
            $gradeName = str_replace('Grade ', '', $gradeName);
            $grade = $this->em->getRepository(Grade::class)->findOneBy(['number' => $gradeName]);
            $this->logger->info("1");
            if (!$grade) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Grade not found'
                );
            }

            $this->logger->info("2");
            $learner->setName($name);
            $learner->setGrade($grade);
            $this->em->persist($learner);
            $this->em->flush();

            $this->logger->info("3");
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

            return array(
                'status' => 'OK',
                'subjects' => $learnerSubjects,
            );
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
                $query = $this->em->createQuery(
                    'SELECT s
                    FROM App\Entity\Subject s
                    WHERE s.active = 1'
                );
            } else {
                $query = $this->em->createQuery(
                    'SELECT s
                    FROM App\Entity\Subject s
                    WHERE s.id NOT IN (:enrolledSubjectIds)
                    AND s.active = 1'
                )->setParameter('enrolledSubjectIds', $enrolledSubjectIds);
            }

            $subjects = $query->getResult();

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

    public function checkLearnerAnswer(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $questionId = $requestBody['question_id'];
            $learnerAnswers = trim($requestBody['answer']);
            $multiLearnerAnswers = $requestBody['answers'];

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
            $isCorrect = !array_udiff($learnerAnswers, $correctAnswers, function ($a, $b) {
                return strcasecmp($a, $b);
            });
            $outcome = $isCorrect ? 'correct' : 'incorrect';

            // Save the result in the Result entity
            $result = new Result();
            $result->setLearner($learner);
            $result->setQuestion($question);
            $result->setOutcome($outcome);
            $this->em->persist($result);
            $this->em->flush();

            $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'subject' => $question->getSubject()]);
            $learnerSubject->setLastUpdated(new \DateTime());
            $this->em->persist($learnerSubject);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'is_correct' => $isCorrect,
                'correct_answers' => implode(', ', $correctAnswers)
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

            if (empty($results)) {
                return array(
                    'status' => 'OK',
                    'percentage' => 0
                );
            }

            $totalQuestions = count($results);
            $correctAnswers = 0;

            foreach ($results as $result) {
                if ($result->getOutcome() === 'correct') {
                    $correctAnswers++;
                }
            }

            $percentage = ($correctAnswers / $totalQuestions);

            $learnerSubject = $this->em->getRepository(Learnersubjects::class)->findOneBy(['learner' => $learner, 'subject' => $subject]);

            if (!$learnerSubject) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Learner subject not found'
                );
            }

            $learnerSubject->setPercentage($percentage);
            $this->em->persist($learnerSubject);
            $this->em->flush();

            return array(
                'status' => 'OK',
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
            $learnerSubjectId = $requestBody['learner_subject_id'];
            $override = $requestBody['override'];

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

            $learnerSubject->setOverideterm($override);
            $this->em->persist($learnerSubject);
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

            if($imageType == 'question') {
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
}
