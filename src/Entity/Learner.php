<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Learner
 *
 * @ORM\Table(name="learner", indexes={@ORM\Index(name="learner_grade_idx", columns={"grade"})})
 * @ORM\Entity
 */
class Learner
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
     * @ORM\Column(name="uid", type="string", length=45, nullable=true)
     */
    private $uid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="overide_term", type="boolean", nullable=false)
     */
    private $overideTerm;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=10, nullable=false, options={"default"="learner"})
     */
    private $role = 'learner';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $created = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastSeen", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $lastseen = 'CURRENT_TIMESTAMP';

    /**
     * @var \Grade
     *
     * @ORM\ManyToOne(targetEntity="Grade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grade", referencedColumnName="id")
     * })
     */
    private $grade;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isOverideTerm(): ?bool
    {
        return $this->overideTerm;
    }

    public function setOverideTerm(bool $overideTerm): static
    {
        $this->overideTerm = $overideTerm;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getLastseen(): ?\DateTimeInterface
    {
        return $this->lastseen;
    }

    public function setLastseen(\DateTimeInterface $lastseen): static
    {
        $this->lastseen = $lastseen;

        return $this;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): static
    {
        $this->grade = $grade;

        return $this;
    }


}
