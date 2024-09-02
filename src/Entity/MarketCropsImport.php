<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * MarketCropsImport
 *
 * @ORM\Table(name="market_crops_import")
 * @ORM\Entity
 */
class MarketCropsImport
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
     * @ORM\Column(name="crop_id", type="integer", nullable=true)
     */
    private $cropId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="crop_name", type="string", length=100, nullable=true)
     */
    private $cropName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="market", type="string", length=45, nullable=true)
     */
    private $market;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCropId(): ?int
    {
        return $this->cropId;
    }

    public function setCropId(?int $cropId): static
    {
        $this->cropId = $cropId;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTimeInterface $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

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

    public function getCropName(): ?string
    {
        return $this->cropName;
    }

    public function setCropName(?string $cropName): static
    {
        $this->cropName = $cropName;

        return $this;
    }

    public function getMarket(): ?string
    {
        return $this->market;
    }

    public function setMarket(?string $market): static
    {
        $this->market = $market;

        return $this;
    }


}
