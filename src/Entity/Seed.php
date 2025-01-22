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
     * @var int|null
     *
     * @ORM\Column(name="crop", type="integer", nullable=true)
     */
    private $crop;

    /**
     * @var string|null
     *
     * @ORM\Column(name="manufacture", type="string", length=45, nullable=true)
     */
    private $manufacture;

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

    public function getCrop(): ?int
    {
        return $this->crop;
    }

    public function setCrop(?int $crop): static
    {
        $this->crop = $crop;

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


}
