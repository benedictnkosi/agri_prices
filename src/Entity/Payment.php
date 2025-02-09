<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment", indexes={@ORM\Index(name="payment_agent_sale", columns={"agent_sale"}), @ORM\Index(name="payment_sale_idx", columns={"sale"})})
 * @ORM\Entity
 */
class Payment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float|null
     *
     * @ORM\Column(name="amount", type="float", precision=10, scale=0, nullable=true)
     */
    private $amount;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sale", type="integer", nullable=true)
     */
    private $sale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="paymentMethod", type="string", length=45, nullable=true)
     */
    private $paymentmethod;

    /**
     * @var int|null
     *
     * @ORM\Column(name="agent_sale", type="integer", nullable=true)
     */
    private $agentSale;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSale(): ?int
    {
        return $this->sale;
    }

    public function setSale(?int $sale): static
    {
        $this->sale = $sale;

        return $this;
    }

    public function getPaymentmethod(): ?string
    {
        return $this->paymentmethod;
    }

    public function setPaymentmethod(?string $paymentmethod): static
    {
        $this->paymentmethod = $paymentmethod;

        return $this;
    }

    public function getAgentSale(): ?int
    {
        return $this->agentSale;
    }

    public function setAgentSale(?int $agentSale): static
    {
        $this->agentSale = $agentSale;

        return $this;
    }


}
