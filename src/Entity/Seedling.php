<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Seedling
 *
 * @ORM\Table(name="seedling", indexes={@ORM\Index(name="seedling_seed_idx", columns={"seed"}), @ORM\Index(name="seedling_grow_mix_idx", columns={"grow_mix"})})
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
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \GrowMix
     *
     * @ORM\ManyToOne(targetEntity="GrowMix")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grow_mix", referencedColumnName="id")
     * })
     */
    private $growMix;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getGrowMix(): ?GrowMix
    {
        return $this->growMix;
    }

    public function setGrowMix(?GrowMix $growMix): static
    {
        $this->growMix = $growMix;

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
