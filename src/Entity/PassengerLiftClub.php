<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PassengerLiftClub
 *
 * @ORM\Table(name="passenger_lift_club", indexes={@ORM\Index(name="passenger_fk_idx", columns={"passenger"}), @ORM\Index(name="lift_club_idx", columns={"lift_club"})})
 * @ORM\Entity
 */
class PassengerLiftClub
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
     * @var \Commuter
     *
     * @ORM\ManyToOne(targetEntity="Commuter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="passenger", referencedColumnName="id")
     * })
     */
    private $passenger;

    /**
     * @var \Liftclub
     *
     * @ORM\ManyToOne(targetEntity="Liftclub")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lift_club", referencedColumnName="id")
     * })
     */
    private $liftClub;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLiftClub(): ?Liftclub
    {
        return $this->liftClub;
    }

    public function setLiftClub(?Liftclub $liftClub): static
    {
        $this->liftClub = $liftClub;

        return $this;
    }


}
