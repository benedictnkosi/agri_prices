<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GrowMix
 *
 * @ORM\Table(name="grow_mix")
 * @ORM\Entity
 */
class GrowMix
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
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;


}
