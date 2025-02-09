<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Sales
 *
 * @ORM\Table(name="sales", indexes={@ORM\Index(name="sale_customer_fk_idx", columns={"customer"}), @ORM\Index(name="sale_farm_idx", columns={"farm"}), @ORM\Index(name="sales_crop_fk_idx", columns={"crop"}), @ORM\Index(name="sales_packaging_idx", columns={"packaging"})})
 * @ORM\Entity
 */
class Sales
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
     * @ORM\Column(name="crop", type="integer", nullable=true)
     */
    private $crop;

    /**
     * @var int|null
     *
     * @ORM\Column(name="customer", type="integer", nullable=true)
     */
    private $customer;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var float|null
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var int|null
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var int|null
     *
     * @ORM\Column(name="farm", type="integer", nullable=true)
     */
    private $farm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="packaging", type="integer", nullable=true)
     */
    private $packaging;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCrop(): ?int
    {
        return $this->crop;
    }

    public function setCrop(?int $crop): static
    {
        $this->crop = $crop;

        return $this;
    }

    public function getCustomer(): ?int
    {
        return $this->customer;
    }

    public function setCustomer(?int $customer): static
    {
        $this->customer = $customer;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
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

    public function getFarm(): ?int
    {
        return $this->farm;
    }

    public function setFarm(?int $farm): static
    {
        $this->farm = $farm;

        return $this;
    }

    public function getPackaging(): ?int
    {
        return $this->packaging;
    }

    public function setPackaging(?int $packaging): static
    {
        $this->packaging = $packaging;

        return $this;
    }


}
