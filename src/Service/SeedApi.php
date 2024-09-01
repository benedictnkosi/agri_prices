<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\Seed;
use App\Entity\Crop;

class SeedApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createSeed(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $name = $requestBody['name'];
            $manufacture = $requestBody['manufacture'];
            $farmUid = $requestBody['farm_uid'];
            $cropId = $requestBody['crop_id'];

            if(empty($name) || empty($manufacture) ||  empty($farmUid) || empty($cropId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Name, manufacture, farm_uid and crop_id values are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $crop = $this->em->getRepository(Crop::class)->findOneBy(['id' => $cropId, 'farm' => $farm]);
            if (!$crop) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Crop not found'
                );
            }

            $seed = new Seed();
            $seed->setName($name);
            $seed->setManufacture($manufacture);
            $seed->setCrop($crop);

            $this->em->persist($seed);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Seed created successfully',
                'id' => $seed->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating seed'
            );
        }
    }

    public function getSeeds(Request $request): array
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
            $queryBuilder = $this->em->createQueryBuilder();

            $query = $queryBuilder
                ->select('s')
                ->from('App\Entity\Seed', 's')
                ->innerJoin('s.crop', 'c')
                ->where('c.farm = :farm')
                ->setParameter('farm', $farm)
                ->getQuery();

                $results = $query->getResult();

                return $results;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting seeds'
            );
        }
    }
    
}
