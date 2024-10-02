<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Farm;
use App\Entity\CashWithdrawal;
use App\Entity\Payment;

class CashApi extends AbstractController
{

    private $em;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    
    public function withdrawCash(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $requestBody = json_decode($request->getContent(), true);
            $amount = $requestBody['amount'];
            $comment = $requestBody['comment'];
            $date = new \DateTimeImmutable($requestBody['date']);
            $farmUid = $requestBody['farm_uid'];

            if (empty($amount) || empty($comment) || empty($date) || empty($farmUid)) {
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


            $cashWithdrawal = new CashWithdrawal();
            $cashWithdrawal->setAmount($amount);
            $cashWithdrawal->setComment($comment);
            $cashWithdrawal->setDate($date);
            $cashWithdrawal->setFarm($farm);

            $this->em->persist($cashWithdrawal);
            $this->em->flush();

            return array(
                'status' => 'OK',
                'message' => 'Withdrawal recorded successfully',
                'id' => $cashWithdrawal->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error adding withdrawal'
            );
        }
    }

    public function getCashReport(Request $request): array
    {
        $this->logger->info("Starting Method: " . __METHOD__);
        try {

            $farmUid = $request->query->get('farm_uid');
            if (empty($farmUid)) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm uid id is required'
                );
            }

            $farm = $this->em->getRepository(Farm::class)->findOneBy(['uid' => $farmUid]);
            if (!$farm) {
                return array(
                    'status' => 'NOK',
                    'message' => 'Farm not found'
                );
            }

            $withdrawals = $this->em->getRepository(CashWithdrawal::class)->findBy(['farm' =>  $farm]);

            $queryBuilder = $this->em->createQueryBuilder();

            $query = $queryBuilder
                ->select('p')
                ->from('App\Entity\Payment', 'p')
                ->innerJoin('p.sale', 's')
                ->where('s.farm = :farm')
                ->where('p.paymentmethod = :paymentMethod')
                ->setParameter('farm', $farm)
                ->setParameter('paymentMethod', 'Cash')
                ->getQuery();

            $payments = $query->getResult();

            $transactions = [];

            // Loop through withdrawals
            foreach ($withdrawals as $withdrawal) {
                $transactions[] = [
                    'date' => $withdrawal->getDate(),
                    'amount' => -$withdrawal->getAmount(),
                    'comment' => $withdrawal->getComment()
                ];
            }

            // Loop through payments
            foreach ($payments as $payment) {
                $transactions[] = [
                    'date' => $payment->getDate(),
                    'amount' => $payment->getAmount(),
                    'comment' => 'Payment: ' . $payment->getSale()->getCustomer()->getName()
                ];
            }

            // Sort the transactions by date
            usort($transactions, function ($a, $b) {
                return $a['date'] <=> $b['date'];
            });

            // Calculate the running balance
            $runningBalance = 0;
            foreach ($transactions as &$transaction) {
                $runningBalance += $transaction['amount'];
                $transaction['running_balance'] = $runningBalance;
            }

            // Sort the transactions by date
            usort($transactions, function ($a, $b) {
                return $b['date'] <=> $a['date'];
            });

            return $transactions;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return array(
                'status' => 'NOK',
                'message' => 'Error getting withdrawals'
            );
        }
    }
}
