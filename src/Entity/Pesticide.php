<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pesticide
 *
 * @ORM\Table(name="pesticide", indexes={@ORM\Index(name="pesticide_farm_fk_idx", columns={"farm"})})
 * @ORM\Entity
 */
class Pesticide
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
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * @var int|null
     *
     * @ORM\Column(name="farm", type="integer", nullable=true)
     */
    private $farm;

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

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getFarm(): ?int
    {
        return $this->farm;
    }

    public function setFarm(?int $farm): static
    {
        $this->farm = $farm;

        return $this;
    }


}
