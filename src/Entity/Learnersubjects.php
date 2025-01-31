<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Learnersubjects
 *
 * @ORM\Table(name="learnersubjects", indexes={@ORM\Index(name="learnersubject_subject_idx", columns={"subject"}), @ORM\Index(name="learnersubject_learner_idx", columns={"learner"})})
 * @ORM\Entity
 */
class Learnersubjects
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
     * @var bool|null
     *
     * @ORM\Column(name="higherGrade", type="boolean", nullable=true)
     */
    private $highergrade = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="overideTerm", type="boolean", nullable=true)
     */
    private $overideterm = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_updated", type="datetime", nullable=true)
     */
    private $lastUpdated;

    /**
     * @var float|null
     *
     * @ORM\Column(name="percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $percentage;

    /**
     * @var \Subject
     *
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subject", referencedColumnName="id")
     * })
     */
    private $subject;

    /**
     * @var \Learner
     *
     * @ORM\ManyToOne(targetEntity="Learner")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="learner", referencedColumnName="id")
     * })
     */
    private $learner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isHighergrade(): ?bool
    {
        return $this->highergrade;
    }

    public function setHighergrade(?bool $highergrade): static
    {
        $this->highergrade = $highergrade;

        return $this;
    }

    public function isOverideterm(): ?bool
    {
        return $this->overideterm;
    }

    public function setOverideterm(?bool $overideterm): static
    {
        $this->overideterm = $overideterm;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(?\DateTimeInterface $lastUpdated): static
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getLearner(): ?Learner
    {
        return $this->learner;
    }

    public function setLearner(?Learner $learner): static
    {
        $this->learner = $learner;

        return $this;
    }


}
