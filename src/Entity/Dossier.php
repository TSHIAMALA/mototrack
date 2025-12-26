<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
#[ORM\Table(name: 'dossier')]
#[ORM\HasLifecycleCallbacks]
class Dossier
{
    public const STATUS_EN_ATTENTE = 'EN_ATTENTE';
    public const STATUS_VALIDE = 'VALIDE';
    public const STATUS_REJETE = 'REJETE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $reference = null;

    #[ORM\ManyToOne(targetEntity: Moto::class)]
    #[ORM\JoinColumn(name: 'moto_id', nullable: false)]
    private ?Moto $moto = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montantTotal = null;

    #[ORM\Column(length: 50)]
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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: Paiement::class, cascade: ['persist', 'remove'])]
    private Collection $paiements;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->reference = $this->generateReference();
    }

    private function generateReference(): string
    {
        return 'DOS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
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

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): static
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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setDossier($this);
        }
        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            if ($paiement->getDossier() === $this) {
                $paiement->setDossier(null);
            }
        }
        return $this;
    }

    public function calculerMontantTotal(): string
    {
        $total = 0;
        foreach ($this->paiements as $paiement) {
            $total += (float) $paiement->getMontant();
        }
        $this->montantTotal = (string) $total;
        return $this->montantTotal;
    }

    public function valider(User $validator): static
    {
        $this->status = self::STATUS_VALIDE;
        $this->validatedBy = $validator;
        $this->validatedAt = new \DateTime();
        
        foreach ($this->paiements as $paiement) {
            $paiement->valider($validator);
        }
        
        return $this;
    }

    public function rejeter(User $validator, string $commentaire): static
    {
        $this->status = self::STATUS_REJETE;
        $this->validatedBy = $validator;
        $this->validatedAt = new \DateTime();
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getMotard(): ?Motard
    {
        return $this->moto?->getMotard();
    }
}
