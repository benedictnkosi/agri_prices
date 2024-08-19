<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FertilizerStock
 *
 * @ORM\Table(name="fertilizer_stock", indexes={@ORM\Index(name="fertilizer_stock_fertilizer_idx", columns={"fertilizer"})})
 * @ORM\Entity
 */
class FertilizerStock
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
     * @var \Fertilizer
     *
     * @ORM\ManyToOne(targetEntity="Fertilizer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fertilizer", referencedColumnName="id")
     * })
     */
    private $fertilizer;


}
