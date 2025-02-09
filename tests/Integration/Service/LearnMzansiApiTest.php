<?php

namespace App\Tests\Integration\Service;

use App\Entity\Grade;
use App\Entity\Learner;
use App\Entity\Subject;
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
} 