<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * PesticideSpraying
 *
 * @ORM\Table(name="pesticide_spraying", indexes={@ORM\Index(name="fertilizer_stock_fertilizer_idx", columns={"pesticide"})})
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
     * @var \Fertilizer
     *
     * @ORM\ManyToOne(targetEntity="Fertilizer")
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

    public function getPesticide(): ?Fertilizer
    {
        return $this->pesticide;
    }

    public function setPesticide(?Fertilizer $pesticide): static
    {
        $this->pesticide = $pesticide;

        return $this;
    }


}
