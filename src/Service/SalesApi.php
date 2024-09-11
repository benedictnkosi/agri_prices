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
            $requestBody = json_decode($request->getContent(), true);

            $customerId = $requestBody['customer_id'];
            $price = $requestBody['price'];
            $date = new \DateTimeImmutable($requestBody['date']);
            $cropId = $requestBody['crop_id'];
            $packagingId = $requestBody['packaging_id'];
            $quantity = $requestBody['quantity'];
            $farmUid = $requestBody['farm_uid'];
            $paid = $requestBody['paid'];


            if (empty($customerId) || empty($price) || empty($date) || empty($cropId) || empty($quantity) || empty($farmUid)) {
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

            $crop = $this->em->getRepository(Crop::class)->findOneBy(['id' => $cropId, 'farm' => $farm]);
            if (!$crop) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Crop not found'
                );
            }

            $packaging = $this->em->getRepository(Packaging::class)->findOneBy(['id' => $packagingId]);
            if (!$packaging) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Packaging not found'
                );
            }

            $sale = new Sales();
            $sale->setCrop($crop);
            $sale->setCustomer($Customer);
            $sale->setFarm($farm);
            $sale->setPrice($price);
            $sale->setDate($date);
            $sale->setQuantity($quantity);
            $sale->setPackaging($packaging);

            $this->em->persist($sale);
            $this->em->flush();

            if($paid == "true"){
                $payment = new Payment();
                $payment->setSale($sale);
                $payment->setAmount($price * $quantity);
                $payment->setDate($date);
                $payment->setPaymentMethod($requestBody['payment_method']);

                $this->em->persist($payment);
                $this->em->flush();
            }
            return array(
                'status' => 'OK',
                'message' => 'Sale recorded successfully',
                'id' => $sale->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error recording sales'
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

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $queryBuilder = $this->em->createQueryBuilder();

            $query = $queryBuilder
                ->select('s.id AS sale_id', 'c.name AS crop_name', 'cust.name AS customer_name', 'pack.name AS packaging', 's.date', 's.price', 's.quantity', 'IDENTITY(s.farm) AS farm')
                ->addSelect('COALESCE(SUM(p.amount), 0) AS total_payments')
                ->from('App\Entity\Sales', 's')
                ->leftJoin('s.crop', 'c')
                ->leftJoin('s.customer', 'cust')
                ->leftJoin('s.packaging', 'pack')
                ->leftJoin('App\Entity\Payment', 'p', Join::WITH, 's.id = p.sale')
                ->where('s.farm = :farm')
                ->groupBy('s.id', 'c.name', 'cust.name')  // Group by crop and customer name to avoid aggregation issues
                ->setParameter('farm', $farm)
                ->orderBy('s.date', 'DESC')
                ->setMaxResults(100)
                ->getQuery();


            $results = $query->getResult();

            return $results;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting sales'
            );
        }
    }

    public function addPayment(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $requestBody = json_decode($request->getContent(), true);
            $amount = $requestBody['amount'];
            $paymentMethod = $requestBody['paymentMethod'];
            $date = new \DateTimeImmutable($requestBody['date']);
            $farmUid = $requestBody['farm_uid'];
            $saleId = isset($requestBody['sale_id']) ? $requestBody['sale_id'] : null;
            $agentSaleId = isset($requestBody['agent_sale_id']) ? $requestBody['agent_sale_id'] : null;

            if (empty($amount) || empty($date) || empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'All fields are required'
                );
            }

            if ($agentSaleId === null && $saleId === null) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Sale id is required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $payment = new Payment();

            if (!empty($saleId)){
                $sale = $this->em->getRepository(Sales::class)->findOneBy(['id' => $saleId]);
                if (!$sale) {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Sale not found'
                    );
                }
                $payment->setSale($sale);
            }else{
                $sale = $this->em->getRepository(AgentSales::class)->findOneBy(['id' => $agentSaleId]);
                if (!$sale) {
                    return array(
                        'status' => 'NOK',
                        'message' => 'Sale not found'
                    );
                }
                $payment->setAgentSale($sale);
            }
            
            $payment->setAmount($amount);
            $payment->setDate($date);
            $payment->setPaymentMethod($paymentMethod);

            $this->em->persist($payment);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Payment recorded successfully',
                'id' => $payment->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error adding payment'
            );
        }
    }

    public function getPayments(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $saleId = $request->query->get('sale_id');
            if (empty($saleId)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Sale id is required'
                );
            }
            
            $sale = $this->em->getRepository(Farm::class)->findOneBy(['id' => $saleId]);
            if (!$sale) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Sale not found'
                );
            }

            $payments = $this->em->getRepository(Payment::class)->findBy(['sale' =>  $sale]);
            return $payments;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting payments'
            );
        }
    }
}
