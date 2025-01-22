<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Learnersubjects
 *
 * @ORM\Table(name="learnersubjects", indexes={@ORM\Index(name="learnersubject_learner_idx", columns={"learner"}), @ORM\Index(name="learnersubject_subject_idx", columns={"subject"})})
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
