<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SeedStock
 *
 * @ORM\Table(name="seed_stock", indexes={@ORM\Index(name="seed_stock_seed_idx", columns={"seed"})})
 * @ORM\Entity
 */
class SeedStock
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
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \Seed
     *
     * @ORM\ManyToOne(targetEntity="Seed")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="seed", referencedColumnName="id")
     * })
     */
    private $seed;


}
