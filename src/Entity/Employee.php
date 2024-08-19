<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Employee
 *
 * @ORM\Table(name="employee")
 * @ORM\Entity
 */
class Employee
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="surname", type="string", length=32, nullable=true)
     */
    private $surname;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createTime = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     *
     * @ORM\Column(name="bank", type="string", length=25, nullable=true)
     */
    private $bank;

    /**
     * @var string|null
     *
     * @ORM\Column(name="account", type="string", length=45, nullable=true)
     */
    private $account;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone_number", type="string", length=10, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="employeecol", type="string", length=45, nullable=true)
     */
    private $employeecol;

    /**
     * @var string|null
     *
     * @ORM\Column(name="gender", type="string", length=45, nullable=true)
     */
    private $gender;


}
