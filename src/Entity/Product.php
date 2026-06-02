<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Processus>
     */
    #[ORM\OneToMany(targetEntity: Processus::class, mappedBy: 'product', cascade: ['remove'])]
    private Collection $processuses;

    public function __construct()
    {
        $this->processuses = new ArrayCollection();
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

    /**
     * @return Collection<int, Processus>
     */
    public function getProcessuses(): Collection
    {
        return $this->processuses;
    }

    public function addProcessus(Processus $processus): static
    {
        if (!$this->processuses->contains($processus)) {
            $this->processuses->add($processus);
            $processus->setProduct($this);
        }
        return $this;
    }

    public function removeProcessus(Processus $processus): static
    {
        if ($this->processuses->removeElement($processus)) {
            if ($processus->getProduct() === $this) {
                $processus->setProduct(null);
            }
        }
        return $this;
    }
}