<?php

namespace App\Tests\Integration\Service;

use App\Entity\Grade;
use App\Entity\Learner;
use App\Entity\Subject;
use App\Entity\Question;
use App\Entity\Result;
use App\Service\LearnMzansiApi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class LearnMzansiApiTest extends KernelTestCase
{
    private $entityManager;
    private $learnMzansiApi;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->learnMzansiApi = $kernel->getContainer()->get(LearnMzansiApi::class);
    }

    public function testCreateSubject(): void
    {
        // Create a test grade first
        $grade = new Grade();
        $grade->setNumber('10');
        $grade->setActive(true);
        $this->entityManager->persist($grade);
        $this->entityManager->flush();

        // Create admin user for authentication
        $admin = new Learner();
        $admin->setUid('admin_test');
        $admin->setRole('admin');
        $admin->setOverideTerm(true);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        // Prepare test data
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            json_encode([
                'name' => 'Test Subject ' . uniqid(),
                'grade' => '10',
                'uid' => 'admin_test'  // Add uid to request body
            ])
        );

        // Execute test
        $result = $this->learnMzansiApi->createSubject($request);

        // Assert results
        $this->assertEquals('OK', $result['status']);
        $this->assertEquals('Successfully created subject', $result['message']);
        $this->assertArrayHasKey('subject_id', $result);
    }

    public function testUpdateSubjectActive(): void
    {
        // Create test data
        $grade = new Grade();
        $grade->setNumber('10');
        $grade->setActive(true);
        $this->entityManager->persist($grade);

        $subject = new Subject();
        $subject->setName('Test Subject');
        $subject->setGrade($grade);
        $subject->setActive(true);
        $this->entityManager->persist($subject);

        $admin = new Learner();
        $admin->setUid('admin_test');
        $admin->setRole('admin');
        $admin->setOverideTerm(true);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        // Prepare request
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            json_encode([
                'subject_id' => $subject->getId(),
                'active' => false,
                'uid' => 'admin_test'  // Add uid to request body
            ])
        );

        // Execute test
        $result = $this->learnMzansiApi->updateSubjectActive($request);

        // Assert results
        $this->assertEquals('OK', $result['status']);
        $this->assertEquals('Successfully updated subject active status', $result['message']);

        // Verify the subject was actually updated
        $updatedSubject = $this->entityManager->getRepository(Subject::class)->find($subject->getId());
        $this->assertFalse($updatedSubject->isActive());
    }

    public function testGetQuestionsCapturedPerWeek(): void
    {
        // Create admin user
        $admin = new Learner();
        $admin->setUid('admin_test');
        $admin->setRole('admin');
        $admin->setOverideTerm(true);
        $this->entityManager->persist($admin);

        // Create a grade
        $grade = new Grade();
        $grade->setNumber('10');
        $grade->setActive(true);
        $this->entityManager->persist($grade);

        // Create a subject
        $subject = new Subject();
        $subject->setName('Test Subject');
        $subject->setGrade($grade);
        $subject->setActive(true);
        $this->entityManager->persist($subject);

        $this->entityManager->flush();

        // Create some test questions
        $capturer1 = 'capturer1@test.com';
        $capturer2 = 'capturer2@test.com';

        // Create questions for capturer1
        for ($i = 0; $i < 3; $i++) {
            $question = new Question();
            $question->setQuestion("Test question $i");
            $question->setType('multiple_choice');
            $question->setSubject($subject);
            $question->setAnswer(json_encode(['answer']));
            $question->setCapturer($capturer1);
            $question->setCreated(new \DateTime());
            $this->entityManager->persist($question);
        }

        // Create questions for capturer2
        for ($i = 0; $i < 2; $i++) {
            $question = new Question();
            $question->setQuestion("Test question $i");
            $question->setType('multiple_choice');
            $question->setSubject($subject);
            $question->setAnswer(json_encode(['answer']));
            $question->setCapturer($capturer2);
            $question->setCreated(new \DateTime());
            $this->entityManager->persist($question);
        }

        $this->entityManager->flush();

        // Create request with admin authentication
        $request = new Request([], [], [], [], [], [], json_encode(['uid' => 'admin_test']));

        // Execute test
        $result = $this->learnMzansiApi->getQuestionsCapturedPerWeek($request);

        // Assert results
        $this->assertEquals('OK', $result['status']);
        $this->assertCount(2, $result['data']);
        $this->assertEquals(5, $result['total_questions']);

        // Verify individual capturer counts
        $capturerCounts = array_column($result['data'], 'count', 'capturer');
        $this->assertEquals(3, $capturerCounts[$capturer1]);
        $this->assertEquals(2, $capturerCounts[$capturer2]);
    }

    public function testGetTopIncorrectQuestions(): void
    {
        // Create test data
        $grade = new Grade();
        $grade->setNumber('10');
        $grade->setActive(true);
        $this->entityManager->persist($grade);

        $subject = new Subject();
        $subject->setName('Test Subject');
        $subject->setGrade($grade);
        $subject->setActive(true);
        $this->entityManager->persist($subject);

        $learner = new Learner();
        $learner->setUid('test_learner');
        $learner->setOverideTerm(true);
        $this->entityManager->persist($learner);

        // Create questions with different failure rates
        $questions = [];
        for ($i = 0; $i < 6; $i++) {
            $question = new Question();
            $question->setQuestion("Test question $i");
            $question->setType('multiple_choice');
            $question->setSubject($subject);
            $question->setAnswer(json_encode(['answer']));
            $question->setActive(true);
            $question->setStatus('approved');
            $question->setCreated(new \DateTime());
            $this->entityManager->persist($question);
            $questions[] = $question;
        }

        $this->entityManager->flush();

        // Create results with different failure rates
        $testData = [
            // [total attempts, incorrect attempts] - resulting in different failure rates
            [10, 9],  // 90% failure rate
            [10, 8],  // 80% failure rate
            [10, 7],  // 70% failure rate
            [10, 6],  // 60% failure rate
            [10, 5],  // 50% failure rate
            [10, 4]   // 40% failure rate - shouldn't appear in top 5
        ];

        foreach ($questions as $index => $question) {
            $totalAttempts = $testData[$index][0];
            $incorrectAttempts = $testData[$index][1];

            // Create incorrect attempts
            for ($i = 0; $i < $incorrectAttempts; $i++) {
                $result = new Result();
                $result->setLearner($learner);
                $result->setQuestion($question);
                $result->setOutcome('incorrect');
                $result->setCreated(new \DateTime());
                $this->entityManager->persist($result);
            }

            // Create correct attempts
            for ($i = 0; $i < ($totalAttempts - $incorrectAttempts); $i++) {
                $result = new Result();
                $result->setLearner($learner);
                $result->setQuestion($question);
                $result->setOutcome('correct');
                $result->setCreated(new \DateTime());
                $this->entityManager->persist($result);
            }
        }

        $this->entityManager->flush();

        // Execute test
        $request = new Request();
        $result = $this->learnMzansiApi->getTopIncorrectQuestions($request);

        // Assert results
        $this->assertEquals('OK', $result['status']);
        $this->assertCount(5, $result['data']); // Should only return top 5

        // Verify order and failure rates
        $this->assertEquals(90, $result['data'][0]['failure_rate']);
        $this->assertEquals(80, $result['data'][1]['failure_rate']);
        $this->assertEquals(70, $result['data'][2]['failure_rate']);
        $this->assertEquals(60, $result['data'][3]['failure_rate']);
        $this->assertEquals(50, $result['data'][4]['failure_rate']);
    }

    public function testGetLearnersCreatedPerWeek(): void
    {
        // Create learners with different creation dates
        $dates = [
            'monday this week',
            'tuesday this week',
            'wednesday this week'
        ];

        $expectedCounts = [
            'monday this week' => 3,
            'tuesday this week' => 2,
            'wednesday this week' => 1
        ];

        foreach ($dates as $date) {
            $dateObj = new \DateTime($date);
            // Create multiple learners for each date
            for ($i = 0; $i < $expectedCounts[$date]; $i++) {
                $learner = new Learner();
                $learner->setUid('learner_' . uniqid());
                $learner->setOverideTerm(true);
                $learner->setCreated($dateObj);
                $this->entityManager->persist($learner);
            }
        }

        $this->entityManager->flush();

        // Execute test
        $request = new Request();
        $result = $this->learnMzansiApi->getLearnersCreatedPerWeek($request);

        // Assert results
        $this->assertEquals('OK', $result['status']);
        $this->assertCount(3, $result['data']); // Should have 3 days of data
        $this->assertEquals(6, $result['total_learners']); // Total of all learners

        // Verify daily counts
        $mondayCount = array_values(array_filter($result['data'], function ($item) {
            return date('l', strtotime($item['date'])) === 'Monday';
        }))[0]['count'];
        $this->assertEquals(3, $mondayCount);

        $tuesdayCount = array_values(array_filter($result['data'], function ($item) {
            return date('l', strtotime($item['date'])) === 'Tuesday';
        }))[0]['count'];
        $this->assertEquals(2, $tuesdayCount);

        $wednesdayCount = array_values(array_filter($result['data'], function ($item) {
            return date('l', strtotime($item['date'])) === 'Wednesday';
        }))[0]['count'];
        $this->assertEquals(1, $wednesdayCount);
    }
}
