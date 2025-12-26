<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\Table(name: 'paiement')]
#[ORM\HasLifecycleCallbacks]
class Paiement
{
    public const STATUS_EN_ATTENTE = 'EN_ATTENTE';
    public const STATUS_VALIDE = 'VALIDE';

    public const MODE_CASH = 'CASH';
    public const MODE_MOBILE_MONEY = 'MOBILE_MONEY';
    public const MODE_VIREMENT = 'VIREMENT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Dossier::class, inversedBy: 'paiements')]
    #[ORM\JoinColumn(name: 'dossier_id', nullable: true)]
    private ?Dossier $dossier = null;

    #[ORM\ManyToOne(targetEntity: Moto::class, inversedBy: 'paiements')]
    #[ORM\JoinColumn(name: 'moto_id', nullable: false)]
    #[Assert\NotNull]
    private ?Moto $moto = null;

    #[ORM\ManyToOne(targetEntity: Taxe::class, inversedBy: 'paiements')]
    #[ORM\JoinColumn(name: 'taxe_id', nullable: false)]
    #[Assert\NotNull]
    private ?Taxe $taxe = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $montant = null;

    #[ORM\Column(name: 'mode_paiement', length: 50, nullable: true)]
    private ?string $modePaiement = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = self::STATUS_EN_ATTENTE;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'validated_by', nullable: true)]
    private ?User $validatedBy = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'validated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $validatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): static
    {
        $this->dossier = $dossier;
        return $this;
    }

    public function getMoto(): ?Moto
    {
        return $this->moto;
    }

    public function setMoto(?Moto $moto): static
    {
        $this->moto = $moto;
        return $this;
    }

    public function getTaxe(): ?Taxe
    {
        return $this->taxe;
    }

    public function setTaxe(?Taxe $taxe): static
    {
        $this->taxe = $taxe;
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

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?string $modePaiement): static
    {
        $this->modePaiement = $modePaiement;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function isValide(): bool
    {
        return $this->status === self::STATUS_VALIDE;
    }

    public function isEnAttente(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getValidatedBy(): ?User
    {
        return $this->validatedBy;
    }

    public function setValidatedBy(?User $validatedBy): static
    {
        $this->validatedBy = $validatedBy;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getValidatedAt(): ?\DateTimeInterface
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeInterface $validatedAt): static
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }

    public function valider(User $validator): static
    {
        $this->status = self::STATUS_VALIDE;
        $this->validatedBy = $validator;
        $this->validatedAt = new \DateTime();
        return $this;
    }
}
