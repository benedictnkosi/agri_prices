<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question", indexes={@ORM\Index(name="question_learner_idx", columns={"higher_grade"}), @ORM\Index(name="question_subject_idx", columns={"subject"})})
 * @ORM\Entity
 */
class Question
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
     * @ORM\Column(name="question", type="text", length=0, nullable=true)
     */
    private $question;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="context", type="text", length=0, nullable=true)
     */
    private $context;

    /**
     * @var string|null
     *
     * @ORM\Column(name="answer", type="text", length=16777215, nullable=true)
     */
    private $answer;

    /**
     * @var array|null
     *
     * @ORM\Column(name="options", type="json", nullable=true)
     */
    private $options;

    /**
     * @var int|null
     *
     * @ORM\Column(name="term", type="integer", nullable=true)
     */
    private $term;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_path", type="string", length=100, nullable=true)
     */
    private $imagePath;

    /**
     * @var string|null
     *
     * @ORM\Column(name="explanation", type="text", length=0, nullable=true)
     */
    private $explanation;

    /**
     * @var int|null
     *
     * @ORM\Column(name="higher_grade", type="smallint", nullable=true)
     */
    private $higherGrade;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="active", type="boolean", nullable=true, options={"default"="1"})
     */
    private $active = true;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=false)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="answer_image", type="string", length=100, nullable=false)
     */
    private $answerImage;

    /**
     * @var \Subject
     *
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subject", referencedColumnName="id")
     * })
     */
    private $subject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getTerm(): ?int
    {
        return $this->term;
    }

    public function setTerm(?int $term): static
    {
        $this->term = $term;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): static
    {
        $this->explanation = $explanation;

        return $this;
    }

    public function getHigherGrade(): ?int
    {
        return $this->higherGrade;
    }

    public function setHigherGrade(?int $higherGrade): static
    {
        $this->higherGrade = $higherGrade;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getAnswerImage(): ?string
    {
        return $this->answerImage;
    }

    public function setAnswerImage(string $answerImage): static
    {
        $this->answerImage = $answerImage;

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


}
