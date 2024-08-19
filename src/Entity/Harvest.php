<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Harvest
 *
 * @ORM\Table(name="harvest", indexes={@ORM\Index(name="harvest_plnating_idx", columns={"planting"})})
 * @ORM\Entity
 */
class Harvest
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
     * @var \Planting
     *
     * @ORM\ManyToOne(targetEntity="Planting")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="planting", referencedColumnName="id")
     * })
     */
    private $planting;


}
