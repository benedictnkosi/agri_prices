<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Units
 *
 * @ORM\Table(name="units", indexes={@ORM\Index(name="units_property", columns={"property"})})
 * @ORM\Entity
 */
class Units
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
     * @var int
     *
     * @ORM\Column(name="rent", type="integer", nullable=false)
     */
    private $rent = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="listed", type="boolean", nullable=true)
     */
    private $listed = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=false, options={"default"="active"})
     */
    private $status = 'active';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="parking", type="boolean", nullable=true)
     */
    private $parking;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="children_allowed", type="boolean", nullable=true)
     */
    private $childrenAllowed;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_occupants", type="integer", nullable=true)
     */
    private $maxOccupants;

    /**
     * @var int|null
     *
     * @ORM\Column(name="min_gross_salary", type="integer", nullable=true)
     */
    private $minGrossSalary;

    /**
     * @var int|null
     *
     * @ORM\Column(name="bedrooms", type="integer", nullable=true, options={"default"="1"})
     */
    private $bedrooms = 1;

    /**
     * @var int|null
     *getRental
     * @ORM\Column(name="bathrooms", type="integer", nullable=true, options={"default"="1"})
     */
    private $bathrooms = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="guid", type="string", length=36, nullable=false)
     */
    private $guid;


    /**
     * @var Properties
     *
     * @ORM\ManyToOne(targetEntity="Properties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="property", referencedColumnName="id")
     * })
     */
    private $property;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getRent(): int|string
    {
        return $this->rent;
    }

    /**
     * @param int $rent
     */
    public function setRent(int|string $rent): void
    {
        $this->rent = $rent;
    }

    /**
     * @return bool|null
     */
    public function getListed(): bool|string|null
    {
        return $this->listed;
    }

    /**
     * @param bool|null $listed
     */
    public function setListed(bool|string|null $listed): void
    {
        $this->listed = $listed;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool|null
     */
    public function getParking(): ?bool
    {
        return $this->parking;
    }

    /**
     * @param bool|null $parking
     */
    public function setParking(?bool $parking): void
    {
        $this->parking = $parking;
    }

    /**
     * @return bool|null
     */
    public function getChildrenAllowed(): ?bool
    {
        return $this->childrenAllowed;
    }

    /**
     * @param bool|null $childrenAllowed
     */
    public function setChildrenAllowed(?bool $childrenAllowed): void
    {
        $this->childrenAllowed = $childrenAllowed;
    }

    /**
     * @return int|null
     */
    public function getMaxOccupants(): ?int
    {
        return $this->maxOccupants;
    }

    /**
     * @param int|null $maxOccupants
     */
    public function setMaxOccupants(?int $maxOccupants): void
    {
        $this->maxOccupants = $maxOccupants;
    }

    /**
     * @return int|null
     */
    public function getMinGrossSalary(): ?int
    {
        return $this->minGrossSalary;
    }

    /**
     * @param int|null $minGrossSalary
     */
    public function setMinGrossSalary(?int $minGrossSalary): void
    {
        $this->minGrossSalary = $minGrossSalary;
    }

    /**
     * @return int|null
     */
    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    /**
     * @param int|null $bedrooms
     */
    public function setBedrooms(?int $bedrooms): void
    {
        $this->bedrooms = $bedrooms;
    }

    /**
     * @return int|null
     */
    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    /**
     * @param int|null $bathrooms
     */
    public function setBathrooms(?int $bathrooms): void
    {
        $this->bathrooms = $bathrooms;
    }

    /**
     * @return Properties
     */
    public function getProperty(): Properties
    {
        return $this->property;
    }

    /**
     * @param Properties $property
     */
    public function setProperty(Properties $property): void
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid(string $guid): void
    {
        $this->guid = $guid;
    }

}
