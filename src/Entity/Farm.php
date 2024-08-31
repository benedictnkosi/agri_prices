<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Farm
 *
 * @ORM\Table(name="farm")
 * @ORM\Entity
 */
class Farm
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
     * @var bool|null
     *
     * @ORM\Column(name="allow_registration", type="boolean", nullable=true)
     */
    private $allowRegistration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uid", type="string", length=45, nullable=true)
     */
    private $uid;

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

    public function isAllowRegistration(): ?bool
    {
        return $this->allowRegistration;
    }

    public function setAllowRegistration(?bool $allowRegistration): static
    {
        $this->allowRegistration = $allowRegistration;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }


}
