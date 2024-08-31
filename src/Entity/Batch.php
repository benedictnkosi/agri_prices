<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Batch
 *
 * @ORM\Table(name="batch", indexes={@ORM\Index(name="seedling_seed_idx", columns={"seed"}), @ORM\Index(name="batch_farm_fk_idx", columns={"farm"}), @ORM\Index(name="batch_crop_fk_idx", columns={"crop"})})
 * @ORM\Entity
 */
class Batch
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
     * @ORM\Column(name="seedling_date", type="datetime", nullable=true)
     */
    private $seedlingDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="grow_mix", type="integer", nullable=true)
     */
    private $growMix;

    /**
     * @var \Crop
     *
     * @ORM\ManyToOne(targetEntity="Crop")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="crop", referencedColumnName="id")
     * })
     */
    private $crop;

    /**
     * @var \Farm
     *
     * @ORM\ManyToOne(targetEntity="Farm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="farm", referencedColumnName="id")
     * })
     */
    private $farm;

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

    public function getSeedlingDate(): ?\DateTimeInterface
    {
        return $this->seedlingDate;
    }

    public function setSeedlingDate(?\DateTimeInterface $seedlingDate): static
    {
        $this->seedlingDate = $seedlingDate;

        return $this;
    }

    public function getGrowMix(): ?int
    {
        return $this->growMix;
    }

    public function setGrowMix(?int $growMix): static
    {
        $this->growMix = $growMix;

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

    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    public function setFarm(?Farm $farm): static
    {
        $this->farm = $farm;

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
