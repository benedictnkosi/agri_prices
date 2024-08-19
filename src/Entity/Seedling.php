<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Seedling
 *
 * @ORM\Table(name="seedling", indexes={@ORM\Index(name="seedling_grow_mix_idx", columns={"grow_mix"}), @ORM\Index(name="seedling_seed_idx", columns={"seed"})})
 * @ORM\Entity
 */
class Seedling
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
     * @var \GrowMix
     *
     * @ORM\ManyToOne(targetEntity="GrowMix")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grow_mix", referencedColumnName="id")
     * })
     */
    private $growMix;

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
