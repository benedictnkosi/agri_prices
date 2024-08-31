<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\Customer;

class CustomerApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function createCustomer(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {
            $name = $request->query->get('name');
            $contactPerson = $request->query->get('contact_person');
            $phoneNumber = $request->query->get('phone_number');
            $farmUid = $request->query->get('farm_uid');

            if (empty($name) || empty($contactPerson) || empty($phoneNumber) || empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Name, contact_person and phone_number values are required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $Customer = $this->em->getRepository(Customer::class)->findOneBy(['name' => $name, 'farm' => $farm]);
            if ($Customer) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Customer with the same name already exists'
                );
            }

            $customer = new Customer();
            $customer->setName($name);
            $customer->setContactPerson($contactPerson);
            $customer->setContactNumber($phoneNumber);
            $customer->setFarm($farm);

            $this->em->persist($customer);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Customer created successfully',
                'id' => $customer->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error creating customer'
            );
        }
    }

    public function getCustomers(Request $request): array
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

            $customers = $this->em->getRepository(Customer::class)->findBy(['farm' => $farmUid]);
            return $customers;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting customers'
            );
        }
    }
    
}
