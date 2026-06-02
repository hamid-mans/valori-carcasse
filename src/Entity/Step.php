<?php

namespace App\Entity;

use App\Repository\StepRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StepRepository::class)]
class Step
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $amout = null;

    #[ORM\Column]
    private ?bool $isGain = null;

    #[ORM\ManyToOne(targetEntity: Processus::class, inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Processus $processus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAmout(): ?float
    {
        return $this->amout;
    }

    public function setAmout(float $amout): static
    {
        $this->amout = $amout;
        return $this;
    }

    public function isGain(): ?bool
    {
        return $this->isGain;
    }

    public function setIsGain(bool $isGain): static
    {
        $this->isGain = $isGain;
        return $this;
    }

    public function getProcessus(): ?Processus
    {
        return $this->processus;
    }

    public function setProcessus(?Processus $processus): static
    {
        $this->processus = $processus;
        return $this;
    }
}