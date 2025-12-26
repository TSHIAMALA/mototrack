<?php

namespace App\Entity;

use App\Repository\MotardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MotardRepository::class)]
#[ORM\Table(name: 'motard')]
#[ORM\HasLifecycleCallbacks]
class Motard
{
    public const TYPE_PHYSIQUE = 'PHYSIQUE';
    public const TYPE_MORALE = 'MORALE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, enumType: null)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TYPE_PHYSIQUE, self::TYPE_MORALE])]
    private ?string $type = null;

    #[ORM\Column(name: 'nom_complet', length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private ?string $nomComplet = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $identification = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'motard', targetEntity: Adresse::class, cascade: ['persist', 'remove'])]
    private Collection $adresses;

    #[ORM\OneToMany(mappedBy: 'motard', targetEntity: Moto::class)]
    private Collection $motos;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
        $this->motos = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    
public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): static
    {
        $this->nomComplet = $nomComplet;
        return $this;
    }

    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    public function setIdentification(?string $identification): static
    {
        $this->identification = $identification;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
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

    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdresse(Adresse $adresse): static
    {
        if (!$this->adresses->contains($adresse)) {
            $this->adresses->add($adresse);
            $adresse->setMotard($this);
        }
        return $this;
    }

    public function removeAdresse(Adresse $adresse): static
    {
        if ($this->adresses->removeElement($adresse)) {
            if ($adresse->getMotard() === $this) {
                $adresse->setMotard(null);
            }
        }
        return $this;
    }

    public function getActiveAdresse(): ?Adresse
    {
        foreach ($this->adresses as $adresse) {
            if ($adresse->isActive()) {
                return $adresse;
            }
        }
        return null;
    }

    public function getMotos(): Collection
    {
        return $this->motos;
    }

    public function __toString(): string
    {
        return $this->nomComplet ?? '';
    }
}
