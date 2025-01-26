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
     * @var int|null
     *
     * @ORM\Column(name="pesticide", type="integer", nullable=true)
     */
    private $pesticide;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="batch", type="integer", nullable=true)
     */
    private $batch;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPesticide(): ?int
    {
        return $this->pesticide;
    }

    public function setPesticide(?int $pesticide): static
    {
        $this->pesticide = $pesticide;

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

    public function getBatch(): ?int
    {
        return $this->batch;
    }

    public function setBatch(?int $batch): static
    {
        $this->batch = $batch;

        return $this;
    }


}
