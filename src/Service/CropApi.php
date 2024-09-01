<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\Crop;

class CropApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createCrop(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $name = $requestBody['name'];
            $farmUid = $requestBody['farm_uid'];

            if(empty($name) || empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Name and farm_uid values are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $crop = new Crop();
            $crop->setName($name);
            $crop->setFarm($farm);

            $this->em->persist($crop);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Crop created successfully',
                'id' => $crop->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating customer'
            );
        }
    }

    public function getCrops(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
        
            $farmUid = $request->query->get('farm_uid');

            if (empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm uid values are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $crops = $this->em->getRepository(Crop::class)->findBy(['farm' => $farm]);
            return $crops;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting crops'
            );
        }
    }
    
}
