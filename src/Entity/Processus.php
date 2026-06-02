<?php

namespace App\Entity;

use App\Repository\ProcessusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcessusRepository::class)]
class Processus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'processuses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    /**
     * @var Collection<int, Step>
     */
    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'processus', cascade: ['remove'])]
    private Collection $steps;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return Collection<int, Step>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setProcessus($this);
        }
        return $this;
    }

    public function removeStep(Step $step): static
    {
        if ($this->steps->removeElement($step)) {
            if ($step->getProcessus() === $this) {
                $step->setProcessus(null);
            }
        }
        return $this;
    }

    // Helpers utiles pour Twig
    public function getGainsTotal(): float
    {
        $total = 0;
        foreach ($this->getSteps() as $step) {
            if ($step->isGain()) { // Vérifie bien que ta méthode s'appelle isGain (ou getIsGain)
                $total += $step->getAmout(); // Attention aux fautes de frappe sur getAmout / getAmount
            }
        }
        return $total;
    }

    public function getCostsTotal(): float
    {
        $total = 0;
        foreach ($this->getSteps() as $step) {
            if (!$step->isGain()) {
                $total += $step->getAmout();
            }
        }
        return $total;
    }

    public function getSoldeFinal(): float
    {
        return $this->getGainsTotal() - $this->getCostsTotal();
    }

    public function setSoldeFinal(float $soldeFinal): static
    {
        $this->soldeFinal = $soldeFinal;

        return $this;
    }
}