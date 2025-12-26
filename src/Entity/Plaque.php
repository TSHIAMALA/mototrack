<?php

namespace App\Entity;

use App\Repository\PlaqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlaqueRepository::class)]
#[ORM\Table(name: 'plaque')]
#[UniqueEntity(fields: ['numero'], message: 'Ce numéro de plaque existe déjà')]
class Plaque
{
    public const STATUT_DISPONIBLE = 'DISPONIBLE';
    public const STATUT_AFFECTEE = 'AFFECTEE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $numero = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $statut = self::STATUT_DISPONIBLE;

    #[ORM\OneToOne(mappedBy: 'plaque', targetEntity: AffectationPlaque::class)]
    private ?AffectationPlaque $affectation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function isDisponible(): bool
    {
        return $this->statut === self::STATUT_DISPONIBLE;
    }

    public function isAffectee(): bool
    {
        return $this->statut === self::STATUT_AFFECTEE;
    }

    public function getAffectation(): ?AffectationPlaque
    {
        return $this->affectation;
    }

    public function setAffectation(?AffectationPlaque $affectation): static
    {
        $this->affectation = $affectation;
        return $this;
    }

    public function __toString(): string
    {
        return $this->numero ?? '';
    }
}
