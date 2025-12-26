<?php

namespace App\Entity;

use App\Repository\AffectationPlaqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AffectationPlaqueRepository::class)]
#[ORM\Table(name: 'affectation_plaque')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['moto'], message: 'Cette moto a déjà une plaque affectée')]
#[UniqueEntity(fields: ['plaque'], message: 'Cette plaque est déjà affectée')]
class AffectationPlaque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'affectationPlaque', targetEntity: Moto::class)]
    #[ORM\JoinColumn(name: 'moto_id', nullable: false, unique: true)]
    #[Assert\NotNull]
    private ?Moto $moto = null;

    #[ORM\OneToOne(inversedBy: 'affectation', targetEntity: Plaque::class)]
    #[ORM\JoinColumn(name: 'plaque_id', nullable: false, unique: true)]
    #[Assert\NotNull]
    private ?Plaque $plaque = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'validated_by', nullable: false)]
    private ?User $validatedBy = null;

    #[ORM\Column(name: 'validated_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $validatedAt = null;

    public function __construct()
    {
        $this->validatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlaque(): ?Plaque
    {
        return $this->plaque;
    }

    public function setPlaque(?Plaque $plaque): static
    {
        $this->plaque = $plaque;
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

    public function getValidatedAt(): ?\DateTimeInterface
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(\DateTimeInterface $validatedAt): static
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }
}
