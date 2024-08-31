<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\Customer;
use App\Entity\Crop;
use App\Entity\Sales;

class SalesApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function recordSale(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $customerId = $request->query->get('customer_id');
            $price = $request->query->get('price');
            $date = new \DateTimeImmutable($request->query->get('date'));
            $cropId = $request->query->get('crop_id');
            $quantity = $request->query->get('quantity');
            $farmUid = $request->query->get('farm_uid');


            if(empty($customerId) || empty($price) || empty($date) || empty($cropId) || empty($quantity) || empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'All fields are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $Customer = $this->em->getRepository(Customer::class)->findOneBy(['id' => $customerId]);
            if (!$Customer) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Customer not found'
                );
            }

            $crop = $this->em->getRepository(Crop::class)->findOneBy(['id' => $cropId]);
            if (!$crop) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Crop not found'
                );
            }

            $sale = new Sales();
            $sale->setCrop($crop);
            $sale->setCustomer($Customer);
            $sale->setFarm($farm);
            $sale->setPrice($price);
            $sale->setDate($date);
            $sale->setQuantity($quantity);

            $this->em->persist($sale);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Sale recorded successfully',
                'id' => $sale->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating customer'
            );
        }
    }


    public function getSales(Request $request): array
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

            $sales = $this->em->getRepository(Sales::class)->findBy(['farm' =>  $farmUid]);
            return $sales;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting sales'
            );
        }
    }
}
