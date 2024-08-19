<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * ToolRegister
 *
 * @ORM\Table(name="tool_register", indexes={@ORM\Index(name="tool_register_tools_idx", columns={"tool"})})
 * @ORM\Entity
 */
class ToolRegister
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
     * @var string|null
     *
     * @ORM\Column(name="person", type="string", length=45, nullable=true)
     */
    private $person;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="returned", type="boolean", nullable=true)
     */
    private $returned;

    /**
     * @var \Tools
     *
     * @ORM\ManyToOne(targetEntity="Tools")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tool", referencedColumnName="id")
     * })
     */
    private $tool;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPerson(): ?string
    {
        return $this->person;
    }

    public function setPerson(?string $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function isReturned(): ?bool
    {
        return $this->returned;
    }

    public function setReturned(?bool $returned): static
    {
        $this->returned = $returned;

        return $this;
    }

    public function getTool(): ?Tools
    {
        return $this->tool;
    }

    public function setTool(?Tools $tool): static
    {
        $this->tool = $tool;

        return $this;
    }


}
