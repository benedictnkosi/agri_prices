<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * DurbanMarket
 *
 * @ORM\Table(name="durban_market")
 * @ORM\Entity
 */
class DurbanMarket
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
     * @var string|null
     *
     * @ORM\Column(name="commodity", type="string", length=45, nullable=true)
     */
    private $commodity;

    /**
     * @var string|null
     *
     * @ORM\Column(name="weight", type="decimal", precision=5, scale=0, nullable=true)
     */
    private $weight;

    /**
     * @var string|null
     *
     * @ORM\Column(name="size_grade", type="string", length=45, nullable=true)
     */
    private $sizeGrade;

    /**
     * @var string|null
     *
     * @ORM\Column(name="container", type="string", length=45, nullable=true)
     */
    private $container;

    /**
     * @var string|null
     *
     * @ORM\Column(name="province", type="string", length=45, nullable=true)
     */
    private $province;

    /**
     * @var string|null
     *
     * @ORM\Column(name="low_price", type="decimal", precision=5, scale=0, nullable=true)
     */
    private $lowPrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="high_price", type="decimal", precision=5, scale=0, nullable=true)
     */
    private $highPrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="average_price", type="decimal", precision=5, scale=0, nullable=true)
     */
    private $averagePrice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sales_total", type="integer", nullable=true)
     */
    private $salesTotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_quantity_sold", type="integer", nullable=true)
     */
    private $totalQuantitySold;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_kg_sold", type="integer", nullable=true)
     */
    private $totalKgSold;

    /**
     * @var int|null
     *
     * @ORM\Column(name="stock_on_hand", type="integer", nullable=true)
     */
    private $stockOnHand;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommodity(): ?string
    {
        return $this->commodity;
    }

    public function setCommodity(?string $commodity): static
    {
        $this->commodity = $commodity;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getSizeGrade(): ?string
    {
        return $this->sizeGrade;
    }

    public function setSizeGrade(?string $sizeGrade): static
    {
        $this->sizeGrade = $sizeGrade;

        return $this;
    }

    public function getContainer(): ?string
    {
        return $this->container;
    }

    public function setContainer(?string $container): static
    {
        $this->container = $container;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getLowPrice(): ?string
    {
        return $this->lowPrice;
    }

    public function setLowPrice(?string $lowPrice): static
    {
        $this->lowPrice = $lowPrice;

        return $this;
    }

    public function getHighPrice(): ?string
    {
        return $this->highPrice;
    }

    public function setHighPrice(?string $highPrice): static
    {
        $this->highPrice = $highPrice;

        return $this;
    }

    public function getAveragePrice(): ?string
    {
        return $this->averagePrice;
    }

    public function setAveragePrice(?string $averagePrice): static
    {
        $this->averagePrice = $averagePrice;

        return $this;
    }

    public function getSalesTotal(): ?int
    {
        return $this->salesTotal;
    }

    public function setSalesTotal(?int $salesTotal): static
    {
        $this->salesTotal = $salesTotal;

        return $this;
    }

    public function getTotalQuantitySold(): ?int
    {
        return $this->totalQuantitySold;
    }

    public function setTotalQuantitySold(?int $totalQuantitySold): static
    {
        $this->totalQuantitySold = $totalQuantitySold;

        return $this;
    }

    public function getTotalKgSold(): ?int
    {
        return $this->totalKgSold;
    }

    public function setTotalKgSold(?int $totalKgSold): static
    {
        $this->totalKgSold = $totalKgSold;

        return $this;
    }

    public function getStockOnHand(): ?int
    {
        return $this->stockOnHand;
    }

    public function setStockOnHand(?int $stockOnHand): static
    {
        $this->stockOnHand = $stockOnHand;

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


}
