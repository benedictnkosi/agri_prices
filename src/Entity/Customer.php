<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="customer", indexes={@ORM\Index(name="customer_farm_fk_idx", columns={"farm"})})
 * @ORM\Entity
 */
class Customer
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
     * @ORM\Column(name="contact_person", type="string", length=45, nullable=true)
     */
    private $contactPerson;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_number", type="string", length=45, nullable=true)
     */
    private $contactNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="agent", type="boolean", nullable=false)
     */
    private $agent;

    /**
     * @var \Farm
     *
     * @ORM\ManyToOne(targetEntity="Farm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="farm", referencedColumnName="id")
     * })
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

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function setContactPerson(?string $contactPerson): static
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }

    public function setContactNumber(?string $contactNumber): static
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    public function isAgent(): ?bool
    {
        return $this->agent;
    }

    public function setAgent(bool $agent): static
    {
        $this->agent = $agent;

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


}
