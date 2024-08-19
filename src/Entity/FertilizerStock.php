<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * FertilizerStock
 *
 * @ORM\Table(name="fertilizer_stock", indexes={@ORM\Index(name="fertilizer_stock_fertilizer_idx", columns={"fertilizer"})})
 * @ORM\Entity
 */
class FertilizerStock
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
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \Fertilizer
     *
     * @ORM\ManyToOne(targetEntity="Fertilizer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fertilizer", referencedColumnName="id")
     * })
     */
    private $fertilizer;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getFertilizer(): ?Fertilizer
    {
        return $this->fertilizer;
    }

    public function setFertilizer(?Fertilizer $fertilizer): static
    {
        $this->fertilizer = $fertilizer;

        return $this;
    }


}
