<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tools
 *
 * @ORM\Table(name="tools")
 * @ORM\Entity
 */
class Tools
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


}
