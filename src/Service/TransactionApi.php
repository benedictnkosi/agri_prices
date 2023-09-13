<?php

namespace App\Service;

use App\Entity\BankAccount;
use App\Entity\Leases;
use App\Entity\Properties;
use App\Entity\Propertyusers;
use App\Entity\Tenant;
use App\Entity\Transaction;
use App\Entity\Units;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use PhpImap\Exceptions\ConnectionException;
use Psr\Log\LoggerInterface;
use SecIT\ImapBundle\Service\Imap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionApi extends AbstractController
{

    private $em;
    private $logger;
    private $authApi;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
        $this->authApi = new AuthApi($this->em, $this->logger);
        if (session_id() === '') {
            $logger->info("Session id is empty" . __METHOD__);
            session_start();
        }
    }

    #[ArrayShape(['result_message' => "string", 'result_code' => "int"])]
    public function addTransaction($leaseId, $amount, $description, $transactionDate, $emailID = 0): array
    {
        $this->logger->debug("Starting Method: " . __METHOD__);
        $responseArray = array();
        try {
            $lease = $this->em->getRepository(Leases::class)->findOneBy(array('guid' => $leaseId));
            if ($lease == null) {
                return array(
                    'result_message' => "Lease not found",
                    'result_code' => 1
                );
            }

            if (strlen($amount) < 1 || !is_numeric($amount) || strlen($amount) > 10) {
                $this->logger->debug("Amount " . $amount);
                return array(
                    'result_message' => "Amount is invalid",
                    'result_code' => 1
                );
            }

            if (strlen($description) < 1 || strlen($description) > 100) {
                return array(
                    'result_message' => "Description length is invalid",
                    'result_code' => 1
                );
            }

            if (!DateTime::createFromFormat('Y-m-d', $transactionDate)) {
                return array(
                    'result_code' => 1,
                    'result_message' => "Transaction date is not valid",
                );
            }

            //check that email not already imported
            if($emailID > 0){
                $transaction = $this->em->getRepository(Transaction::class)->findOneBy(array('emailId' => $emailID));
                if($transaction !== null){
                    return array(
                        'result_message' => "Transaction already added",
                        'result_code' => 0
                    );
                }
            }

            $transaction = new Transaction();
            $transaction->setDate(new DateTime($transactionDate));
            $transaction->setAmount(intval($amount));
            $transaction->setDescription($description);
            $transaction->setLease($lease);
            $transaction->setEmailId($emailID);
            $transaction->setGuid($this->generateGuid());

            $this->em->persist($transaction);
            $this->em->flush($transaction);

            $subject = "";
            $message = "";
            if(strcmp($description, "Thank you for payment") == 0){
                $balance = $this->getBalanceDue($lease->getId())["result_message"];
                $message = "Thank you for payment " .$amount . " , Balance: " . $balance ;
                $subject = "Aluve App - Thank you for payment";

            }elseif(str_contains($description, "Rent")){
                $balance = $this->getBalanceDue($lease->getId())["result_message"];
                $message = "Rent of R$amount has been added to your account. Balance: " . $balance ;
                $subject = "Aluve App - Rent Added To Account";
            }elseif(strcmp($description, "Late Rent Payment Fee") == 0){
                $balance = $this->getBalanceDue($lease->getId())["result_message"];
                $message = "Late rent payment fee of R$amount has been added to your account. Balance: " . $balance ;
                $subject = "Aluve App - Late Fee Added To Account";
            }elseif(strcmp($description, "Application Fee") == 0){
                $message = "Application fee of R$amount has been added to your account.";
                $subject = "Aluve App - Application Fee";
            }

            if(strlen($message) > 0){
                $link =  "https://" . $_SERVER['HTTP_HOST'] . "/tenant";
                $linkText = "View Statement";
                $template = "generic";
                $communicationApi = new CommunicationApi($this->em, $this->logger);
                $communicationApi->sendEmail($lease->getTenant()->getEmail(), $lease->getTenant()->getName(),$subject , $message, $link, $linkText, $template);
            }

            return array(
                'result_message' => "Successfully added transaction",
                'result_code' => 0
            );

        } catch (Exception $ex) {
            $this->logger->error("Error " . print_r($responseArray, true));
            return array(
                'result_message' => $ex->getMessage(),
                'result_code' => 1
            );
        }
    }

    public function getTransactions($guid, $isLandlord): array
    {
        $this->logger->debug("Starting Method: " . __METHOD__);
        $responseArray = array();
        try {
            $lease = $this->em->getRepository(Leases::class)->findOneBy(array('guid' => $guid));

            $transactions = $this->em->getRepository(Transaction::class)->findBy(array('lease' => $lease), array('date' => 'DESC'));
            if (sizeof($transactions) < 1) {
                return array(
                    'result_message' => "0",
                    'result_code' => 1
                );
            }
            $balance = 0;
            $isLoggedIn = $this->getUser() !== null;
            foreach ($transactions as $transaction) {
                $balance = $balance + intval($transaction->getAmount());
                $responseArray[] = array(
                    'description' => $transaction->getDescription(),
                    'date' => $transaction->getDate()->format("Y-m-d"),
                    'amount' => number_format($transaction->getAmount(), 2, '.', ''),
                    'balance' =>  number_format($balance, 2, '.', ''),
                    'logged_in' => $isLoggedIn,
                    'id' => $transaction->getId(),
                    'guid' => $transaction->getGuid(),
                    'isLandlord' => $isLandlord
                );
            }

            return $responseArray;
        } catch (Exception $ex) {
            $this->logger->error("Error " . print_r($responseArray, true));
            return array(
                'result_message' => $ex->getMessage(),
                'result_code' => 1
            );
        }
    }

    #[ArrayShape(['result_message' => "string", 'result_code' => "int"])]
    public function deleteTransaction($guid): array
    {
        $this->logger->debug("Starting Method: " . __METHOD__);
        $responseArray = array();
        try {
            $transaction = $this->em->getRepository(Transaction::class)->findOneBy(array('guid' => $guid));

            if ($transaction == null) {
                return array(
                    'result_message' => "Transaction not found",
                    'result_code' => 1
                );
            }

            $auth = $this->authApi->isAuthorisedToChangeLease($transaction->getLease()->getGuid());
            if($auth["result_code"] == 1){return $auth;}

            $this->em->remove($transaction);
            $this->em->flush($transaction);

            return array(
                'result_message' => "Successfully removed transaction",
                'result_code' => 0
            );
        } catch (Exception $ex) {
            $this->logger->error("Error " . print_r($responseArray, true));
            return array(
                'result_message' => $ex->getMessage(),
                'result_code' => 1
            );
        }
    }

    #[ArrayShape(['result_message' => "string", 'result_code' => "int"])]
    public function getBalanceDue($leaseId): array
    {
        $this->logger->debug("Starting Method: " . __METHOD__);
        $responseArray = array();
        try {
            $transactions = $this->em->getRepository(Transaction::class)->findBy(array('lease' => $leaseId));
            if (sizeof($transactions) < 1) {
                return array(
                    'result_message' => "0",
                    'result_code' => 1
                );
            }

            $total = 0;

            foreach ($transactions as $transaction) {
                $total = $total + intval($transaction->getAmount());
            }


            return array(
                'result_message' => $total,
                'result_code' => 0
            );
        } catch (Exception $ex) {
            $this->logger->error("Error " . print_r($responseArray, true));
            return array(
                'result_message' => $ex->getMessage(),
                'result_code' => 1
            );
        }
    }

    public function getBalanceDueForAllActiveLeases($propertyGuid): array|int
    {
        $this->logger->debug("Starting Method: " . __METHOD__);
        $responseArray = array();
        try {

            $property = $this->em->getRepository(Properties::class)->findOneBy(array('guid' =>  $propertyGuid));
            if ($property == null) {
                return 0;
            }

            $leases = $this->em->getRepository(Leases::class)->findBy(array('status' => "active", 'property'=> $property->getId()));
            $totalDue = 0;
            foreach ($leases as $lease) {
                $response = $this->getBalanceDue($lease->getId());
                $totalDue += intval($response["result_message"]);
            }

            return array(
                'total_due' => $totalDue,
                'result_code' => 0
            );
        } catch (Exception $ex) {
            $this->logger->error("Error " . print_r($responseArray, true));
            return array(
                'result_message' => $ex->getMessage(),
                'result_code' => 1
            );
        }
    }

    /**
     * @throws ConnectionException
     */
    function importTransactions(Imap $imap): JsonResponse|array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        $responseArray = array();
        try {
        // testing with a boolean response
        $isConnectable = $imap->testConnection('full_config_connection');

        if(!$isConnectable) {
            $this->logger->info("Connection to mail failed");
            return array(
                'result_message' => "Connection to mail failed",
                'result_code' => 1
            );
        }

        $this->logger->info("Connection to mail worked");

        $exampleConnection = $imap->get('full_config_connection');

        // testing with a full error message

            $search = 'ON ' . date('d-M-Y') . ' BODY "paid to"';
            $emails = $exampleConnection->searchMailbox($search);
            $now = new DateTime();
            $leaseFound = "no";
            if ($emails) {
                $this->logger->info("Emails found");
                foreach ($emails as $emailID) {
                    $this->logger->info("Emails id $emailID");
                    $email = $exampleConnection->getMail($emailID);
                    $emailSubject = $email->subject;
                    $startOfAmountIndex = strpos($emailSubject, ":-)") + 5;
                    $endOfAmountIndex = strpos($emailSubject, " paid to ");
                    $amount = substr($emailSubject, $startOfAmountIndex, $endOfAmountIndex - $startOfAmountIndex);

                    $startOfRefIndex = strpos($emailSubject, "Ref.") + 4;
                    $endOfRefIndex = strrpos($emailSubject, ". ");
                    $ref = substr($emailSubject, $startOfRefIndex, $endOfRefIndex - $startOfRefIndex);

                    $startOfAccountIndex = strpos($emailSubject, "c..") + 4;
                    $endOfAccountIndex = strrpos($emailSubject, " @");
                    $partialAccountNumber = substr($emailSubject, $startOfAccountIndex, $endOfAccountIndex - $startOfAccountIndex);

                    $leases = $this->em->getRepository(Leases::class)->findBy(array('status' => 'active'));

                    if(sizeof($leases) > 0){
                        foreach ($leases as $lease){
                            $bankAccount = $this->em->getRepository(BankAccount::class)->findOneBy(array('id' => $lease->getUnit()->getProperty()->getBankAccount()));
                            if($bankAccount === null){
                                continue;
                            }
                            $bankAccountNumber = $bankAccount->getAccountNumber();
                            $this->logger->info("account number " . $bankAccountNumber);
                            $this->logger->info("partial account number " . $partialAccountNumber);
                            $this->logger->info("ref " . $ref);
                            $this->logger->info("lease pay rule " . $lease->getPaymentRules());
                            if(str_contains(strtolower($ref), strtolower($lease->getPaymentRules())) && strlen($lease->getPaymentRules()>1)){
                                if(str_contains($bankAccountNumber, $partialAccountNumber)){
                                    $this->logger->info("Leases found matching payment reference");
                                    $response = $this->addTransaction($lease->getId(), $amount * -1, "Thank you for payment - $ref", $now->format("Y-m-d"), $emailID);
                                    $this->logger->info(print_r($response,true));
                                    $leaseFound = "yes";
                                }else{
                                    $this->logger->info("account does not match");
                                }
                            }else{
                                $this->logger->info("ref does not match");
                            }
                        }
                    }else{
                        $this->logger->info("No leases found matching payment reference");
                    }

                    $responseArray[] =  array(
                        'amount' => $amount,
                        'ref' => $ref,
                        'subject' =>$emailSubject,
                        'lease_found' => $leaseFound
                    );
                }
            }

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            return new JsonResponse($exception->getMessage(), 200, array());
        }

        return $responseArray;
    }

    function generateGuid(): string
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}