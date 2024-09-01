<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Packaging
 *
 * @ORM\Table(name="packaging", indexes={@ORM\Index(name="packaging_crop_idx", columns={"crop"})})
 * @ORM\Entity
 */
class Packaging
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
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var float|null
     *
     * @ORM\Column(name="weight", type="float", precision=10, scale=0, nullable=true)
     */
    private $weight;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

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
