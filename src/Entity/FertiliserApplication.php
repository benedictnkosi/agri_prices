<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * FertiliserApplication
 *
 * @ORM\Table(name="fertiliser_application", indexes={@ORM\Index(name="fertiliser_app_fertiliser_idx", columns={"fertiliser"}), @ORM\Index(name="fertiliser_app_batch_idx", columns={"batch"})})
 * @ORM\Entity
 */
class FertiliserApplication
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
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var \Fertilizer
     *
     * @ORM\ManyToOne(targetEntity="Fertilizer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fertiliser", referencedColumnName="id")
     * })
     */
    private $fertiliser;

    /**
     * @var \Seedling
     *
     * @ORM\ManyToOne(targetEntity="Seedling")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="batch", referencedColumnName="id")
     * })
     */
    private $batch;

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

    public function getFertiliser(): ?Fertilizer
    {
        return $this->fertiliser;
    }

    public function setFertiliser(?Fertilizer $fertiliser): static
    {
        $this->fertiliser = $fertiliser;

        return $this;
    }

    public function getBatch(): ?Seedling
    {
        return $this->batch;
    }

    public function setBatch(?Seedling $batch): static
    {
        $this->batch = $batch;

        return $this;
    }


}
