<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Seedling
 *
 * @ORM\Table(name="seedling", indexes={@ORM\Index(name="seedling_seed_idx", columns={"seed"})})
 * @ORM\Entity
 */
class Seedling
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="seedling_date", type="datetime", nullable=true)
     */
    private $seedlingDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="transplant_date", type="date", nullable=true)
     */
    private $transplantDate;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="transplanted", type="boolean", nullable=true)
     */
    private $transplanted = '0';

    /**
     * @var \Seed
     *
     * @ORM\ManyToOne(targetEntity="Seed")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="seed", referencedColumnName="id")
     * })
     */
    private $seed;

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

    public function getSeedlingDate(): ?\DateTimeInterface
    {
        return $this->seedlingDate;
    }

    public function setSeedlingDate(?\DateTimeInterface $seedlingDate): static
    {
        $this->seedlingDate = $seedlingDate;

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

    public function isTransplanted(): ?bool
    {
        return $this->transplanted;
    }

    public function setTransplanted(?bool $transplanted): static
    {
        $this->transplanted = $transplanted;

        return $this;
    }

    public function getSeed(): ?Seed
    {
        return $this->seed;
    }

    public function setSeed(?Seed $seed): static
    {
        $this->seed = $seed;

        return $this;
    }


}
