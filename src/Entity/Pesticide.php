<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pesticide
 *
 * @ORM\Table(name="pesticide")
 * @ORM\Entity
 */
class Pesticide
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
     * @var int|null
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;


}
