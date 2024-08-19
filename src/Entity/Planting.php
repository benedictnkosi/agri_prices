<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Planting
 *
 * @ORM\Table(name="planting", indexes={@ORM\Index(name="planting_plot_idx", columns={"plot"}), @ORM\Index(name="plnating_seed_idx", columns={"seedling"})})
 * @ORM\Entity
 */
class Planting
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
     * @var \Plot
     *
     * @ORM\ManyToOne(targetEntity="Plot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plot", referencedColumnName="id")
     * })
     */
    private $plot;

    /**
     * @var \Seedling
     *
     * @ORM\ManyToOne(targetEntity="Seedling")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="seedling", referencedColumnName="id")
     * })
     */
    private $seedling;

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

    public function getPlot(): ?Plot
    {
        return $this->plot;
    }

    public function setPlot(?Plot $plot): static
    {
        $this->plot = $plot;

        return $this;
    }

    public function getSeedling(): ?Seedling
    {
        return $this->seedling;
    }

    public function setSeedling(?Seedling $seedling): static
    {
        $this->seedling = $seedling;

        return $this;
    }


}
