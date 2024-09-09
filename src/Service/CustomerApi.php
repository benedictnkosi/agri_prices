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
            $requestBody = json_decode($request->getContent(), true);
            $name = $requestBody['name'];
            $contactPerson = $requestBody['contact_person'];
            $phoneNumber = $requestBody['phone_number'];
            $farmUid = $requestBody['farm_uid'];
            $isAgent = $requestBody['is_agent'] == "true";

            if (empty($name) || empty($contactPerson) || empty($phoneNumber) || empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Name, contact person and phone number values are required'
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
            $customer->setAgent($isAgent);
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
            $type = $request->query->get('type');

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

            if(empty($type)){
                $customers = $this->em->getRepository(Customer::class)->findBy(['farm' => $farm]);
            }else{
                $agentValue = ($type === 'agent') ? 1 : 0;
                $customers = $this->em->getRepository(Customer::class)->findBy(['farm' => $farm, 'agent' => $agentValue]);
            }
            
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
