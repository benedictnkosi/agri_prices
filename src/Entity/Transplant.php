<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Transplant
 *
 * @ORM\Table(name="transplant", indexes={@ORM\Index(name="transplant_date_fk_idx", columns={"batch"})})
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
     * @var \Batch
     *
     * @ORM\ManyToOne(targetEntity="Batch")
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

    public function getTransplantDate(): ?\DateTimeInterface
    {
        return $this->transplantDate;
    }

    public function setTransplantDate(?\DateTimeInterface $transplantDate): static
    {
        $this->transplantDate = $transplantDate;

        return $this;
    }

    public function getBatch(): ?Batch
    {
        return $this->batch;
    }

    public function setBatch(?Batch $batch): static
    {
        $this->batch = $batch;

        return $this;
    }


}
