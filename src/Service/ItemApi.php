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


class ItemApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function deleteItem(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $requestBody = json_decode($request->getContent(), true);

            $id = intval($requestBody['id']);
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
            
            $errorMessage = 'Error removing item';
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                return array(
                    'status' => 'NOK',
                    'message' => "Integrity constraint violation"
                );
            }

            return array(
                'status' => 'NOK',
                'message' => $errorMessage
            );
        }
    }
}
