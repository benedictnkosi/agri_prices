<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Transplant
 *
 * @ORM\Table(name="transplant", indexes={@ORM\Index(name="transplant_date_fk_idx", columns={"seedling"})})
 * @ORM\Entity
 */
class Transplant
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
     * @ORM\Column(name="seedling", type="integer", nullable=true)
     */
    private $seedling;

    /**
     * @var int|null
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="transplant_date", type="date", nullable=true)
     */
    private $transplantDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="harvest_date", type="date", nullable=true)
     */
    private $harvestDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeedling(): ?int
    {
        return $this->seedling;
    }

    public function setSeedling(?int $seedling): static
    {
        $this->seedling = $seedling;

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

    public function getTransplantDate(): ?\DateTimeInterface
    {
        return $this->transplantDate;
    }

    public function setTransplantDate(?\DateTimeInterface $transplantDate): static
    {
        $this->transplantDate = $transplantDate;

        return $this;
    }

    public function getHarvestDate(): ?\DateTimeInterface
    {
        return $this->harvestDate;
    }

    public function setHarvestDate(?\DateTimeInterface $harvestDate): static
    {
        $this->harvestDate = $harvestDate;

        return $this;
    }


}
