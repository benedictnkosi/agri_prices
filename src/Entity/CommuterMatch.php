<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommuterMatch
 *
 * @ORM\Table(name="commuter_match", indexes={@ORM\Index(name="passenger_idx", columns={"passenger"}), @ORM\Index(name="driver_idx", columns={"driver"})})
 * @ORM\Entity
 */
class CommuterMatch
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
     * @ORM\Column(name="total_trip", type="integer", nullable=true)
     */
    private $totalTrip;

    /**
     * @var int|null
     *
     * @ORM\Column(name="distance_home", type="integer", nullable=true)
     */
    private $distanceHome;

    /**
     * @var int|null
     *
     * @ORM\Column(name="distance_work", type="integer", nullable=true)
     */
    private $distanceWork;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var int|null
     *
     * @ORM\Column(name="additional_time", type="integer", nullable=true)
     */
    private $additionalTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="driver_status", type="string", length=45, nullable=true)
     */
    private $driverStatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="passenger_status", type="string", length=45, nullable=true)
     */
    private $passengerStatus;

    /**
     * @var int|null
     *
     * @ORM\Column(name="duration_home", type="integer", nullable=true)
     */
    private $durationHome;

    /**
     * @var int|null
     *
     * @ORM\Column(name="duration_work", type="integer", nullable=true)
     */
    private $durationWork;

    /**
     * @var string|null
     *
     * @ORM\Column(name="map_link", type="string", length=200, nullable=true)
     */
    private $mapLink;

    /**
     * @var \Commuter
     *
     * @ORM\ManyToOne(targetEntity="Commuter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="passenger", referencedColumnName="id")
     * })
     */
    private $passenger;

    /**
     * @var \Commuter
     *
     * @ORM\ManyToOne(targetEntity="Commuter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="driver", referencedColumnName="id")
     * })
     */
    private $driver;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalTrip(): ?int
    {
        return $this->totalTrip;
    }

    public function setTotalTrip(?int $totalTrip): static
    {
        $this->totalTrip = $totalTrip;

        return $this;
    }

    public function getDistanceHome(): ?int
    {
        return $this->distanceHome;
    }

    public function setDistanceHome(?int $distanceHome): static
    {
        $this->distanceHome = $distanceHome;

        return $this;
    }

    public function getDistanceWork(): ?int
    {
        return $this->distanceWork;
    }

    public function setDistanceWork(?int $distanceWork): static
    {
        $this->distanceWork = $distanceWork;

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

    public function getAdditionalTime(): ?int
    {
        return $this->additionalTime;
    }

    public function setAdditionalTime(?int $additionalTime): static
    {
        $this->additionalTime = $additionalTime;

        return $this;
    }

    public function getDriverStatus(): ?string
    {
        return $this->driverStatus;
    }

    public function setDriverStatus(?string $driverStatus): static
    {
        $this->driverStatus = $driverStatus;

        return $this;
    }

    public function getPassengerStatus(): ?string
    {
        return $this->passengerStatus;
    }

    public function setPassengerStatus(?string $passengerStatus): static
    {
        $this->passengerStatus = $passengerStatus;

        return $this;
    }

    public function getDurationHome(): ?int
    {
        return $this->durationHome;
    }

    public function setDurationHome(?int $durationHome): static
    {
        $this->durationHome = $durationHome;

        return $this;
    }

    public function getDurationWork(): ?int
    {
        return $this->durationWork;
    }

    public function setDurationWork(?int $durationWork): static
    {
        $this->durationWork = $durationWork;

        return $this;
    }

    public function getMapLink(): ?string
    {
        return $this->mapLink;
    }

    public function setMapLink(?string $mapLink): static
    {
        $this->mapLink = $mapLink;

        return $this;
    }

    public function getPassenger(): ?Commuter
    {
        return $this->passenger;
    }

    public function setPassenger(?Commuter $passenger): static
    {
        $this->passenger = $passenger;

        return $this;
    }

    public function getDriver(): ?Commuter
    {
        return $this->driver;
    }

    public function setDriver(?Commuter $driver): static
    {
        $this->driver = $driver;

        return $this;
    }


}
