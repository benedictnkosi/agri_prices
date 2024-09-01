<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\Packaging;
use App\Entity\Crop;

class PackagingApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createPackaging(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $name = $requestBody['name'];
            $weight = $requestBody['weight'];
            $farmUid = $requestBody['farm_uid'];
            $cropId = $requestBody['crop_id'];

            if (empty($name) || empty($weight) || empty($farmUid) || empty($cropId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'All values are required'
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

            $packaging = new Packaging();
            $packaging->setName($name);
            $packaging->setWEight($weight);
            $packaging->setCrop($crop);

            $this->em->persist($packaging);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Packaging created successfully',
                'id' => $packaging->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating packaging'
            );
        }
    }

    public function getPackaging(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
        
            $farmUid = $request->query->get('farm_uid');
            $cropId = $request->query->get('crop_id');

            if (empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm uid and crop id values are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            if($cropId){
                $crop = $this->em->getRepository(Crop::class)->findOneBy(['id' => $cropId, 'farm' => $farm]);
                if (!$crop) {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Crop not found'
                    );
                }
                return $this->em->getRepository(Packaging::class)->findBy(['crop' => $crop]);
            }else{
                $queryBuilder = $this->em->createQueryBuilder();

                $query = $queryBuilder
                    ->select('p')
                    ->from('App\Entity\Packaging', 'p')
                    ->innerJoin('p.crop', 'c')
                    ->where('c.farm = :farm')
                    ->setParameter('farm', $farm)
                    ->getQuery();
                    return $query->getResult();
            }
            
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting packaging'
            );
        }
    }
    
}
