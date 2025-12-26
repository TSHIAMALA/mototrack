<?php

namespace App\Entity;

use App\Repository\TaxeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaxeRepository::class)]
#[ORM\Table(name: 'taxe')]
#[UniqueEntity(fields: ['code'], message: 'Ce code de taxe existe déjà')]
class Taxe
{
    public const CODE_VIGNETTE = 'VIGNETTE';
    public const CODE_PLAQUE = 'PLAQUE';
    public const CODE_TCR = 'TCR';

    public const MONTANT_VIGNETTE = 17.00;
    public const MONTANT_PLAQUE = 32.00;
    public const MONTANT_TCR = 5.00;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $libelle = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $montant = null;

    #[ORM\OneToMany(mappedBy: 'taxe', targetEntity: Paiement::class)]
    private Collection $paiements;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s $', $this->libelle, $this->montant);
    }
}
