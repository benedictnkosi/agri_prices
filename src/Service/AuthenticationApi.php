<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class AuthenticationApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createNewUserIfNotExists(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $email = $data['email'];
        $googleUID = $data['google_uid'];

        $user = $this->em->getRepository(User::class)->findOneBy(['googleuid' => $googleUID]);
        if ($user) {
            return Array(
                'status' => 'OK',
                'message' => 'User already exists with this Google UID' . $googleUID
            );
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setGoogleuid($googleUID);
        $user->setStatus(1);
        $user->setCreatedAt(new \DateTime());
        $user->setRoles("user");

        $this->em->persist($user);
        $this->em->flush();

        return Array(
            'status' => 'OK',
            'message' => 'User created successfully'
        );
    }

    public function getUserByUid($googleUID)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['googleuid' => $googleUID]);
        if (!$user) {
            return Array(
                'status' => 'NOK',
                'message' => 'User not found'
            );
        }

        return $user;
    }
}
