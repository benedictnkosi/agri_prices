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


class AgentApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }


    public function deliverToAgents(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);

            $customerId = $requestBody['customer_id'];
            $date = new \DateTimeImmutable($requestBody['date']);
            $cropId = $requestBody['crop_id'];
            $packagingId = $requestBody['packaging_id'];
            $quantity = $requestBody['quantity'];
            $farmUid = $requestBody['farm_uid'];


            if (empty($customerId) || empty($date) || empty($cropId) || empty($quantity) || empty($farmUid)) {
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

            if (!$Customer->isAgent()) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Customer is not an agent'
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

            $marketDelivery = new MarketDelivery();
            $marketDelivery->setCrop($crop);
            $marketDelivery->setCustomer($Customer);
            $marketDelivery->setFarm($farm);
            $marketDelivery->setDate($date);
            $marketDelivery->setQuantity($quantity);
            $marketDelivery->setPackaging($packaging);

            $this->em->persist($marketDelivery);
            $this->em->flush();

            //record dummy sale to help with query
            $sale = new AgentSales();
            $sale->setPrice(0);
            $sale->setSaleDate($date);
            $sale->setQuantity(0);
            $sale->setDelivery($marketDelivery);

            $this->em->persist($sale);
            $this->em->flush();


            return array(
                'status' => 'OK',
                'message' => 'Market delivery recorded successfully',
                'id' => $marketDelivery->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error recording Market delivery'
            );
        }
    }



    public function getMarketDeliveries(Request $request): array
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

            $marketDeliveries = $this->em->getRepository(MarketDelivery::class)->findBy(['farm' => $farm], ['date' => 'DESC'], 10);

            return $marketDeliveries;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting sales'
            );
        }
    }


    public function recordAgentSale(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);

            $deliveryId = $requestBody['delivery_id'];
            $price = $requestBody['price'];
            $date = new \DateTimeImmutable($requestBody['date']);
            $quantity = $requestBody['quantity'];
            $farmUid = $requestBody['farm_uid'];

            if (empty($deliveryId) || empty($price) || empty($date) || empty($quantity) || empty($farmUid)) {
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

            $delivery = $this->em->getRepository(MarketDelivery::class)->findOneBy(['id' => $deliveryId]);
            if (!$delivery) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Delivery not found'
                );
            }

            if ($date < $delivery->getDate()) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Sale date cannot be earlier than delivery date'
                );
            }

            $agentSalesTotal = $this->em->createQueryBuilder()
                ->select('SUM(s.quantity)')
                ->from('App\Entity\AgentSales', 's')
                ->where('s.delivery = :delivery')
                ->setParameter('delivery', $delivery)
                ->getQuery()
                ->getSingleScalarResult();

            $newSaleQuantity = intval($agentSalesTotal) + intval($quantity);
            if ($newSaleQuantity > $delivery->getQuantity()) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Quantity sold exceeds delivery quantity'
                );
            }


            $sale = new AgentSales();
            $sale->setPrice($price);
            $sale->setSaleDate($date);
            $sale->setQuantity($quantity);
            $sale->setDelivery($delivery);

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
                'message' => 'Error recording sales'
            );
        }
    }

    public function getAgentSales(Request $request)
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $farmUid = $request->query->get('farm_uid');
            $agentId = $request->query->get('agent_id');

            if (empty($farmUid)) {
                return json_encode(array_values(array(
                    'status' => 'NOK',
                    'message' => 'Farm uid values are required'
                )));
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return json_encode(array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                ));
            }

            $queryBuilder = $this->em->createQueryBuilder();

            if ($agentId) {

                $agent = $this->em->getRepository(Customer::class)->findOneBy(['id' => $agentId]);
                if (!$agent) {
                    return json_encode(array(

                    ));
                }

                $query = $queryBuilder
                    ->select('s')
                    ->from('App\Entity\AgentSales', 's')
                    ->innerJoin('s.delivery', 'md')
                    ->where('md.farm = :farm')
                    ->andWhere('md.customer = :agent')
                    ->setParameter('farm', $farm)
                    ->setParameter('agent', $agent)
                    ->orderBy('s.saleDate', 'DESC')
                    ->setMaxResults(100)
                    ->getQuery();
            } else {
                $query = $queryBuilder
                    ->select('s')
                    ->from('App\Entity\AgentSales', 's')
                    ->innerJoin('s.delivery', 'md')
                    ->where('md.farm = :farm')
                    ->setParameter('farm', $farm)
                    ->orderBy('s.saleDate', 'DESC')
                    ->setMaxResults(100)
                    ->getQuery();
            }

            $results = $query->getResult();

            // Group sales by delivery
            $deliveries = [];
            foreach ($results as $sale) {
                $deliveryId = $sale->getDelivery()->getId();
                if (!isset($deliveries[$deliveryId])) {
                    $deliveries[$deliveryId] = [
                        'delivery_date' => $sale->getDelivery()->getDate()->format('Y-m-d'),
                        'crop_name' => $sale->getDelivery()->getCrop()->getName(),
                        'id' => $sale->getDelivery()->getId(),
                        'agent' => $sale->getDelivery()->getCustomer()->getName(),
                        'quantity' => $sale->getDelivery()->getQuantity(),
                        'packaging' => $sale->getDelivery()->getPackaging()->getName(),
                        'sales' => []
                    ];
                }

                // Fetch payments for the sale
                $paymentQueryBuilder = $this->em->createQueryBuilder();
                $paymentQuery = $paymentQueryBuilder
                    ->select('p')
                    ->from('App\Entity\Payment', 'p')
                    ->where('p.agentSale = :sale')
                    ->setParameter('sale', $sale)
                    ->getQuery();

                $payments = $paymentQuery->getResult();

                // Calculate total paid
                $totalPaid = array_reduce($payments, function ($sum, $payment) {
                    return $sum + $payment->getAmount();
                }, 0);

                $deliveries[$deliveryId]['sales'][] = [
                    'id' => $sale->getId(),
                    'quantity' => $sale->getQuantity(),
                    'price' => $sale->getPrice(),
                    'sale_date' => $sale->getSaleDate()->format('Y-m-d'),
                    'total_paid' => $totalPaid,
                    'paid' => $sale->isPaid()
                ];
            }

            // Convert to JSON
            $jsonResult = json_encode(array_values($deliveries), JSON_PRETTY_PRINT);

            return $jsonResult;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return json_encode(array_values(array(
                'status' => 'NOK',
                'message' => 'Error getting sales'
            )));
        }
    }


    public function deleteItem(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);

            $id = $requestBody['id'];
            $entity = $requestBody['entity'];
            $farmUid = $requestBody['farm_uid'];

            if (empty($id) || empty($farmUid)) {
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

            $entityClass = 'App\Entity\\' . ucfirst($entity);
            $item = $this->em->getRepository($entityClass)->findOneBy(['id' => $id]);

            if (!$item) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Item not found'
                );
            }

            $this->em->remove($item);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Item removed successfully',
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error removing item'
            );
        }
    }
}
