<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Harvest
 *
 * @ORM\Table(name="harvest", indexes={@ORM\Index(name="harvest_plnating_idx", columns={"planting"})})
 * @ORM\Entity
 */
class Harvest
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
     * @var \Planting
     *
     * @ORM\ManyToOne(targetEntity="Planting")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="planting", referencedColumnName="id")
     * })
     */
    private $planting;

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

    public function getPlanting(): ?Planting
    {
        return $this->planting;
    }

    public function setPlanting(?Planting $planting): static
    {
        $this->planting = $planting;

        return $this;
    }


}
