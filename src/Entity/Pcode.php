<?php

namespace App\Entity;

use App\Repository\PcodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PcodeRepository::class)]
#[ORM\Table(name: 'pcode')]
#[ORM\Index(name: 'idx_pcode_category', columns: ['pcode_category_id'])]
#[ORM\Index(name: 'idx_parent_pcode', columns: ['parent_pcode'])]
class Pcode
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $pcode = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(name: 'citizen_name', length: 255, nullable: true)]
    private ?string $citizenName = null;

    #[ORM\ManyToOne(targetEntity: PcodeCategory::class, inversedBy: 'pcodes')]
    #[ORM\JoinColumn(name: 'pcode_category_id', nullable: false)]
    #[Assert\NotNull]
    private ?PcodeCategory $pcodeCategory = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_pcode', referencedColumnName: 'pcode', nullable: true)]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getPcode(): ?string
    {
        return $this->pcode;
    }

    public function setPcode(string $pcode): static
    {
        $this->pcode = $pcode;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getCitizenName(): ?string
    {
        return $this->citizenName;
    }

    public function setCitizenName(?string $citizenName): static
    {
        $this->citizenName = $citizenName;
        return $this;
    }

    public function getPcodeCategory(): ?PcodeCategory
    {
        return $this->pcodeCategory;
    }

    public function setPcodeCategory(?PcodeCategory $pcodeCategory): static
    {
        $this->pcodeCategory = $pcodeCategory;
        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function __toString(): string
    {
        return $this->citizenName ?? $this->label ?? $this->pcode ?? '';
    }
}
