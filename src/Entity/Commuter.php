<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commuter
 *
 * @ORM\Table(name="commuter", indexes={@ORM\Index(name="work_address_idx", columns={"work_address"}), @ORM\Index(name="home_address_idx", columns={"home_address"})})
 * @ORM\Entity
 */
class Commuter
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
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=200, nullable=true)
     */
    private $phone;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;

    /**
     * @var int|null
     *
     * @ORM\Column(name="travel_time", type="integer", nullable=true)
     */
    private $travelTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_match", type="datetime", nullable=true)
     */
    private $lastMatch;

    /**
     * @var string|null
     *
     * @ORM\Column(name="banking", type="string", length=200, nullable=true)
     */
    private $banking;

    /**
     * @var string|null
     *
     * @ORM\Column(name="home_departure", type="string", length=10, nullable=true)
     */
    private $homeDeparture;

    /**
     * @var string|null
     *
     * @ORM\Column(name="work_departure", type="string", length=10, nullable=true)
     */
    private $workDeparture;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fuel", type="string", length=10, nullable=true)
     */
    private $fuel;

    /**
     * @var \CommuterAddress
     *
     * @ORM\ManyToOne(targetEntity="CommuterAddress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="work_address", referencedColumnName="id")
     * })
     */
    private $workAddress;

    /**
     * @var \CommuterAddress
     *
     * @ORM\ManyToOne(targetEntity="CommuterAddress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="home_address", referencedColumnName="id")
     * })
     */
    private $homeAddress;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTravelTime(): ?int
    {
        return $this->travelTime;
    }

    public function setTravelTime(?int $travelTime): static
    {
        $this->travelTime = $travelTime;

        return $this;
    }

    public function getLastMatch(): ?\DateTimeInterface
    {
        return $this->lastMatch;
    }

    public function setLastMatch(?\DateTimeInterface $lastMatch): static
    {
        $this->lastMatch = $lastMatch;

        return $this;
    }

    public function getBanking(): ?string
    {
        return $this->banking;
    }

    public function setBanking(?string $banking): static
    {
        $this->banking = $banking;

        return $this;
    }

    public function getHomeDeparture(): ?string
    {
        return $this->homeDeparture;
    }

    public function setHomeDeparture(?string $homeDeparture): static
    {
        $this->homeDeparture = $homeDeparture;

        return $this;
    }

    public function getWorkDeparture(): ?string
    {
        return $this->workDeparture;
    }

    public function setWorkDeparture(?string $workDeparture): static
    {
        $this->workDeparture = $workDeparture;

        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(?string $fuel): static
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getWorkAddress(): ?CommuterAddress
    {
        return $this->workAddress;
    }

    public function setWorkAddress(?CommuterAddress $workAddress): static
    {
        $this->workAddress = $workAddress;

        return $this;
    }

    public function getHomeAddress(): ?CommuterAddress
    {
        return $this->homeAddress;
    }

    public function setHomeAddress(?CommuterAddress $homeAddress): static
    {
        $this->homeAddress = $homeAddress;

        return $this;
    }


}
