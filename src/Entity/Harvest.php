<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Harvest
 *
 * @ORM\Table(name="harvest", indexes={@ORM\Index(name="harvest_crop_fk_idx", columns={"crop"})})
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
     * @var \Crop
     *
     * @ORM\ManyToOne(targetEntity="Crop")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="crop", referencedColumnName="id")
     * })
     */
    private $crop;

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

    public function getCrop(): ?Crop
    {
        return $this->crop;
    }

    public function setCrop(?Crop $crop): static
    {
        $this->crop = $crop;

        return $this;
    }


}
