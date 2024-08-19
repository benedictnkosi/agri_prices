<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PesticideSpraying
 *
 * @ORM\Table(name="pesticide_spraying", indexes={@ORM\Index(name="fertilizer_stock_fertilizer_idx", columns={"pesticide"})})
 * @ORM\Entity
 */
class PesticideSpraying
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
     *   @ORM\JoinColumn(name="pesticide", referencedColumnName="id")
     * })
     */
    private $pesticide;


}
