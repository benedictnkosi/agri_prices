<?php

namespace App\Service;

use App\Entity\AgentSales;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\Customer;
use App\Entity\Crop;
use App\Entity\Sales;
use App\Entity\Payment;
use App\Entity\Packaging;
use App\Entity\MarketDelivery;


class DashboardApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }


    public function getSalesAmountPerDay(Request $request): array
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

            $queryBuilder = $this->em->createQueryBuilder('s')
                ->select('s.date, SUM(s.price * s.quantity) as totalSales')
                ->from('App\Entity\Sales', 's')
                ->where('s.farm = :farm')
                ->setParameter('farm', $farm)
                ->groupBy('s.date')
                ->orderBy('s.date', 'ASC');

            return  $queryBuilder->getQuery()->getResult();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting daily sales'
            );
        }
    }


    public function getAgentSalesAmountPerDay(Request $request): array
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

            
            $queryBuilder = $this->em->createQueryBuilder('s')
                ->select('s.saleDate as date, SUM(s.price * s.quantity) as totalSales')
                ->from('App\Entity\AgentSales', 's')
                ->innerJoin('s.delivery', 'md')
                ->where('md.farm = :farm')
                ->setParameter('farm', $farm)
                ->groupBy('s.saleDate')
                ->orderBy('s.saleDate', 'ASC');

            return  $queryBuilder->getQuery()->getResult();

            
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting daily sales'
            );
        }
    }

    public function getSalesStats(Request $request): array
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

            $currentDate = new \DateTime();
            $startOfMonth = $currentDate->modify('first day of this month')->setTime(0, 0);
            $startOfLastMonth = (clone $startOfMonth)->modify('-1 month');
            $startOfYear = (clone $currentDate)->setDate($currentDate->format('Y'), 1, 1)->setTime(0, 0);

            // Total sales for this month
            $queryBuilder = $this->em->createQueryBuilder();
            $queryBuilder->select('SUM(s.price * s.quantity) as totalSalesThisMonth')
                ->from('App\Entity\Sales', 's')
                ->where('s.farm = :farm')
                ->andWhere('s.date >= :startOfMonth')
                ->setParameter('farm', $farm)
                ->setParameter('startOfMonth', $startOfMonth);

            $totalSalesThisMonth = $queryBuilder->getQuery()->getSingleScalarResult();

            // Total sales for last month
            $queryBuilder = $this->em->createQueryBuilder();
            $queryBuilder->select('SUM(s.price * s.quantity) as totalSalesLastMonth')
                ->from('App\Entity\Sales', 's')
                ->where('s.farm = :farm')
                ->andWhere('s.date >= :startOfLastMonth')
                ->andWhere('s.date < :startOfMonth')
                ->setParameter('farm', $farm)
                ->setParameter('startOfLastMonth', $startOfLastMonth)
                ->setParameter('startOfMonth', $startOfMonth);

            $totalSalesLastMonth = $queryBuilder->getQuery()->getSingleScalarResult();

            // Total sales for this year
            $queryBuilder = $this->em->createQueryBuilder();
            $queryBuilder->select('SUM(s.price * s.quantity) as totalSalesThisYear')
                ->from('App\Entity\Sales', 's')
                ->where('s.farm = :farm')
                ->andWhere('s.date >= :startOfYear')
                ->setParameter('farm', $farm)
                ->setParameter('startOfYear', $startOfYear);

            $totalSalesThisYear = $queryBuilder->getQuery()->getSingleScalarResult();

            return array(
                'status' => 'OK',
                'totalSalesThisMonth' => $totalSalesThisMonth,
                'totalSalesLastMonth' => $totalSalesLastMonth,
                'totalSalesThisYear' => $totalSalesThisYear
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting daily sales'
            );
        }
    }

    public function getWeeklySeedlings(Request $request): array
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
            $queryBuilder->select('sg')
                ->from('App\Entity\Seedling', 'sg')
                ->join('sg.seed', 's')
                ->join('s.crop', 'c')
                ->where('c.farm = :farm')
                ->andWhere('sg.seedlingDate >= :threeMonthsAgo')
                ->setParameter('threeMonthsAgo', new \DateTime('-3 months'))
                ->setParameter('farm', $farm)
                ->orderBy('sg.seedlingDate', 'ASC');

            $results = $queryBuilder->getQuery()->getResult();

            foreach ($results as $result) {
                $weekNumber = $result->getSeedlingDate()->format('W');
                $cropName = $result->getSeed()->getCrop()->getName();
                $quantity = $result->getQuantity();

                if (!isset($groupedResults[$weekNumber])) {
                    $groupedResults[$weekNumber] = [];
                }

                if (!isset($groupedResults[$weekNumber][$cropName])) {
                    $groupedResults[$weekNumber][$cropName] = 0;
                }

                $groupedResults[$weekNumber][$cropName] += $quantity;
            }

            $formattedResults = [];
            $currentDate = new \DateTime(); // Add this line to declare and initialize the $currentDate variable
            foreach ($groupedResults as $weekNumber => $crops) {
                foreach ($crops as $cropName => $quantity) {
                    $formattedResults[] = [
                        'date' => (new \DateTime())->setISODate((int)$currentDate->format('Y'), (int)$weekNumber)->format('Y-m-d'),
                        'crop' => $cropName,
                        'count' => $quantity
                    ];
                }
            }

            return $formattedResults;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting weekly seedlings'
            );
        }
    }


    public function getWeeklyTransplants(Request $request): array
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
            $queryBuilder->select('t')
                ->from('App\Entity\Transplant', 't')
                ->join('t.seedling', 'sg')
                ->join('sg.seed', 's')
                ->join('s.crop', 'c')
                ->where('c.farm = :farm')
                ->andWhere('sg.transplantDate >= :threeMonthsAgo')
                ->setParameter('threeMonthsAgo', new \DateTime('-3 months'))
                ->setParameter('farm', $farm)
                ->orderBy('t.transplantDate', 'ASC');

            $results = $queryBuilder->getQuery()->getResult();

            $groupedResults = [];

            foreach ($results as $result) {
                $weekNumber = $result->getTransplantDate()->format('W');
                $cropName = $result->getSeedling()->getSeed()->getCrop()->getName();
                $quantity = $result->getQuantity();

                if (!isset($groupedResults[$weekNumber])) {
                    $groupedResults[$weekNumber] = [];
                }

                if (!isset($groupedResults[$weekNumber][$cropName])) {
                    $groupedResults[$weekNumber][$cropName] = 0;
                }

                $groupedResults[$weekNumber][$cropName] += $quantity;
            }

            $formattedResults = [];
            $currentDate = new \DateTime(); // Add this line to declare and initialize the $currentDate variable
            foreach ($groupedResults as $weekNumber => $crops) {
                foreach ($crops as $cropName => $quantity) {
                    $formattedResults[] = [
                        'date' => (new \DateTime())->setISODate((int)$currentDate->format('Y'), (int)$weekNumber)->format('Y-m-d'),
                        'crop' => $cropName,
                        'count' => $quantity
                    ];
                }
            }

            return $formattedResults;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting weekly transplant'
            );
        }
    }
}
