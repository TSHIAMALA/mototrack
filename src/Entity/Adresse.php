<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
#[ORM\Table(name: 'adresse')]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Motard::class, inversedBy: 'adresses')]
    #[ORM\JoinColumn(name: 'motard_id', nullable: false)]
    #[Assert\NotNull]
    private ?Motard $motard = null;

    #[ORM\ManyToOne(targetEntity: Pcode::class)]
    #[ORM\JoinColumn(name: 'commune_pcode', referencedColumnName: 'pcode', nullable: true)]
    private ?Pcode $commune = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $quartier = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $avenue = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotard(): ?Motard
    {
        return $this->motard;
    }

    public function setMotard(?Motard $motard): static
    {
        $this->motard = $motard;
        return $this;
    }

    public function getCommune(): ?Pcode
    {
        return $this->commune;
    }

    public function setCommune(?Pcode $commune): static
    {
        $this->commune = $commune;
        return $this;
    }

    public function getQuartier(): ?string
    {
        return $this->quartier;
    }

    public function setQuartier(?string $quartier): static
    {
        $this->quartier = $quartier;
        return $this;
    }

    public function getAvenue(): ?string
    {
        return $this->avenue;
    }

    public function setAvenue(?string $avenue): static
    {
        $this->avenue = $avenue;
        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->numero ? 'N°' . $this->numero : null,
            $this->avenue,
            $this->quartier,
            $this->commune?->getLabel(),
        ]);
        return implode(', ', $parts);
    }

    public function __toString(): string
    {
        return $this->getFullAddress();
    }
}
