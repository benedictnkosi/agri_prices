<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * AgentSales
 *
 * @ORM\Table(name="agent_sales", indexes={@ORM\Index(name="agent_sales_delivery_idx", columns={"delivery"})})
 * @ORM\Entity
 */
class AgentSales
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
     * @var int|null
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var float|null
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="sale_date", type="date", nullable=true)
     */
    private $saleDate;

    /**
     * @var \MarketDelivery
     *
     * @ORM\ManyToOne(targetEntity="MarketDelivery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="delivery", referencedColumnName="id")
     * })
     */
    private $delivery;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSaleDate(): ?\DateTimeInterface
    {
        return $this->saleDate;
    }

    public function setSaleDate(?\DateTimeInterface $saleDate): static
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    public function getDelivery(): ?MarketDelivery
    {
        return $this->delivery;
    }

    public function setDelivery(?MarketDelivery $delivery): static
    {
        $this->delivery = $delivery;

        return $this;
    }


}
