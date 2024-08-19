<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Planting
 *
 * @ORM\Table(name="planting", indexes={@ORM\Index(name="planting_plot_idx", columns={"plot"}), @ORM\Index(name="plnating_seed_idx", columns={"seedling"})})
 * @ORM\Entity
 */
class Planting
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
     * @var \Plot
     *
     * @ORM\ManyToOne(targetEntity="Plot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plot", referencedColumnName="id")
     * })
     */
    private $plot;

    /**
     * @var \Seedling
     *
     * @ORM\ManyToOne(targetEntity="Seedling")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="seedling", referencedColumnName="id")
     * })
     */
    private $seedling;


}
