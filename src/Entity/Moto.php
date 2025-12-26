<?php

namespace App\Entity;

use App\Repository\MotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MotoRepository::class)]
#[ORM\Table(name: 'moto')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['numeroChassis'], message: 'Ce numéro de châssis existe déjà')]
class Moto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Motard::class, inversedBy: 'motos')]
    #[ORM\JoinColumn(name: 'motard_id', nullable: false)]
    #[Assert\NotNull]
    private ?Motard $motard = null;

    #[ORM\ManyToOne(targetEntity: MarqueMoto::class, inversedBy: 'motos')]
    #[ORM\JoinColumn(name: 'marque_id', nullable: false)]
    #[Assert\NotNull]
    private ?MarqueMoto $marque = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $modele = null;

    #[ORM\Column(name: 'numero_chassis', length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $numeroChassis = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $couleur = null;

    #[ORM\ManyToOne(targetEntity: Site::class, inversedBy: 'motos')]
    #[ORM\JoinColumn(name: 'site_id', nullable: false)]
    #[Assert\NotNull]
    private ?Site $site = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'moto', targetEntity: Paiement::class)]
    private Collection $paiements;

    #[ORM\OneToOne(mappedBy: 'moto', targetEntity: AffectationPlaque::class)]
    private ?AffectationPlaque $affectationPlaque = null;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

  
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

    public function getMarque(): ?MarqueMoto
    {
        return $this->marque;
    }

    public function setMarque(?MarqueMoto $marque): static
    {
        $this->marque = $marque;
        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): static
    {
        $this->modele = $modele;
        return $this;
    }

    public function getNumeroChassis(): ?string
    {
        return $this->numeroChassis;
    }

    public function setNumeroChassis(string $numeroChassis): static
    {
        $this->numeroChassis = $numeroChassis;
        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;
        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;
        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function getAffectationPlaque(): ?AffectationPlaque
    {
        return $this->affectationPlaque;
    }

    public function setAffectationPlaque(?AffectationPlaque $affectationPlaque): static
    {
        if ($affectationPlaque !== null && $affectationPlaque->getMoto() !== $this) {
            $affectationPlaque->setMoto($this);
        }
        $this->affectationPlaque = $affectationPlaque;
        return $this;
    }

    public function hasPlaque(): bool
    {
        return $this->affectationPlaque !== null;
    }

    public function getPlaque(): ?Plaque
    {
        return $this->affectationPlaque?->getPlaque();
    }

    public function __toString(): string
    {
        return sprintf('%s %s - %s', $this->marque, $this->modele, $this->numeroChassis);
    }
}
