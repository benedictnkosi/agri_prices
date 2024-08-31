<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Farm;

class FarmApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createFarm(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $joiningCode = bin2hex(random_bytes(16));
            $name = $request->query->get('name');
            $allowRegistration = $request->query->get('allow_registration');

            if (empty($name) || empty($allowRegistration)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Name and allow_registration values are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['name' => $name]);
            if ($farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm with the same name already exists'
                );
            }

            $farm = new Farm();
            $farm->setName($name);
            $farm->setAllowRegistration($allowRegistration);
            $farm->setJoiningCode($joiningCode);

            $this->em->persist($farm);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Farm created successfully',
                'uid' => $joiningCode
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating farm'
            );
        }
    }

    public function JoinFarm(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $joiningCode = $request->query->get('uid');
            $googleUID = $request->query->get('google_uid');

            if (empty($joiningCode) || empty($googleUID)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'joining_code and google_uid values are required'
                );
            }

            $user = $this->em->getRepository(User::class)->findOneBy(['googleuid' => $googleUID]);
            if (!$user) {
                return array(
                    'status' => 'NOK',
                    'message' => 'User not found'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['joiningCode' => $joiningCode]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }


            $user->setFarm($farm);
            $this->em->persist($user);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Farm joined successfully'
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error joining farm'
            );
        }
    }
}
