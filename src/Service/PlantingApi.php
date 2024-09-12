<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\Transplant;
use App\Entity\Seedling;
use App\Entity\Seed;


class PlantingApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createSeedling(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $seedId = $requestBody['seed_id'];
            $quantity = $requestBody['quantity'];
            $seedlingDate = $requestBody['seedling_date'];
            $transplantDate = $requestBody['transplant_date'];

            $farmUid = $requestBody['farm_uid'];

            if (empty($seedId) || empty($quantity) || empty($seedlingDate) || empty($farmUid) || empty($transplantDate)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'All values are required'
                );
            }


            $transplantDateObj = new \DateTime($transplantDate);
            $seedlingDateObj = new \DateTime($seedlingDate);

            if ($transplantDateObj < $seedlingDateObj) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Transplant date cannot be before seedling date'
                );
            }


            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $seed = $this->em->getRepository(Seed::class)->findOneBy(['id' => $seedId]);
            if (!$seed) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Seed not found'
                );
            }

            $seedling = new Seedling();
            $seedling->setSeed($seed);
            $seedling->setQuantity($quantity);
            $seedling->setSeedlingDate(new \DateTime($seedlingDate));
            $seedling->setTransplantDate(new \DateTime($transplantDate));

            $this->em->persist($seedling);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Seedling created successfully',
                'id' => $seedling->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating$seedling'
            );
        }
    }


    public function createTransplant(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);
            $seedlingId = $requestBody['seedling_id'];
            $quantity = $requestBody['quantity'];
            $transplantDate = $requestBody['transplant_date'];
            $harvestDate = $requestBody['harvest_date'];
            $farmUid = $requestBody['farm_uid'];

            if (empty($seedlingId) || empty($quantity) || empty($transplantDate) || empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Seedling, quantity, transplant date and farm uid values are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $seedling = $this->em->getRepository(Seedling::class)->findOneBy(['id' => $seedlingId]);
            if (!$seedling) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Seedling not found'
                );
            }

            $transplantDateObj = new \DateTime($transplantDate);
            $harvestDateObj = new \DateTime($harvestDate);

            if ($harvestDateObj < $transplantDateObj) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Harvest date cannot be before transplant date'
                );
            }

            $transplant = new Transplant();
            $transplant->setSeedling($seedling);
            $transplant->setQuantity($quantity);
            $transplant->setTransplantDate($transplantDateObj);
            $transplant->setHarvestDate(new \DateTime($harvestDate));

            $this->em->persist($transplant);
            $this->em->flush();

            //update seedling status
            $seedling->setTransplanted(true);
            $this->em->persist($seedling);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Transplant created successfully',
                'id' => $seedling->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error transplanting seedlings'
            );
        }
    }

    public function getSeedlings(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $farmUid = $request->query->get('farm_uid');
            $cropId = $request->query->get('crop_id');

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

            if ($cropId) {
                $query = $queryBuilder
                    ->select('b')
                    ->from('App\Entity\Seedling', 'b')
                    ->innerJoin('b.seed', 's')
                    ->innerJoin('s.crop', 'c')
                    ->where('c.farm = :farm')
                    ->andWhere('c.id = :cropId')
                    ->setParameter('farm', $farm)
                    ->setParameter('cropId', $cropId)
                    ->orderBy('b.seedlingDate', 'DESC')
                    ->getQuery();
            } else {
                $query = $queryBuilder
                    ->select('b')
                    ->from('App\Entity\Seedling', 'b')
                    ->innerJoin('b.seed', 's')
                    ->innerJoin('s.crop', 'c')
                    ->where('c.farm = :farm')
                    ->setParameter('farm', $farm)
                    ->orderBy('b.seedlingDate', 'DESC')
                    ->getQuery();
            }

            $results = $query->getResult();

            return $results;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting seedlings'
            );
        }
    }

    public function getTransplants(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $farmUid = $request->query->get('farm_uid');
            $cropId = $request->query->get('crop_id');

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

            if ($cropId) {
                $query = $queryBuilder
                    ->select('t')
                    ->from('App\Entity\Transplant', 't')
                    ->innerJoin('t.seedling', 'b')
                    ->innerJoin('b.seed', 's')
                    ->innerJoin('s.crop', 'c')
                    ->where('c.farm = :farm')
                    ->andWhere('c.id = :cropId')
                    ->setParameter('farm', $farm)
                    ->setParameter('cropId', $cropId)
                    ->orderBy('t.transplantDate', 'DESC')
                    ->getQuery();
            } else {
                $query = $queryBuilder
                    ->select('t')
                    ->from('App\Entity\Transplant', 't')
                    ->innerJoin('t.seedling', 'b')
                    ->innerJoin('b.seed', 's')
                    ->innerJoin('s.crop', 'c')
                    ->where('c.farm = :farm')
                    ->setParameter('farm', $farm)
                    ->orderBy('t.transplantDate', 'DESC')
                    ->getQuery();
            }

            $results = $query->getResult();

            return $results;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting transplants'
            );
        }
    }
}
