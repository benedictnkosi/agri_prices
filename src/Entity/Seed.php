<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Seed
 *
 * @ORM\Table(name="seed", indexes={@ORM\Index(name="seed_crop_fk_idx", columns={"crop"})})
 * @ORM\Entity
 */
class Seed
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
     * @var string|null
     *
     * @ORM\Column(name="manufacture", type="string", length=45, nullable=true)
     */
    private $manufacture;

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

    public function getManufacture(): ?string
    {
        return $this->manufacture;
    }

    public function setManufacture(?string $manufacture): static
    {
        $this->manufacture = $manufacture;

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
