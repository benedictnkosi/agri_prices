<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * PesticideSpraying
 *
 * @ORM\Table(name="pesticide_spraying", indexes={@ORM\Index(name="fertilizer_stock_fertilizer0_idx", columns={"pesticide"}), @ORM\Index(name="persticide_spraying_batch_idx", columns={"batch"})})
 * @ORM\Entity
 */
class PesticideSpraying
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \Seedling
     *
     * @ORM\ManyToOne(targetEntity="Seedling")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="batch", referencedColumnName="id")
     * })
     */
    private $batch;

    /**
     * @var \Pesticide
     *
     * @ORM\ManyToOne(targetEntity="Pesticide")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pesticide", referencedColumnName="id")
     * })
     */
    private $pesticide;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBatch(): ?Seedling
    {
        return $this->batch;
    }

    public function setBatch(?Seedling $batch): static
    {
        $this->batch = $batch;

        return $this;
    }

    public function getPesticide(): ?Pesticide
    {
        return $this->pesticide;
    }

    public function setPesticide(?Pesticide $pesticide): static
    {
        $this->pesticide = $pesticide;

        return $this;
    }


}
