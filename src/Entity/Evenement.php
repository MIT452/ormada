<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 220)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $datedudebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $datedufin = null;
    
    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Media::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }
    
      public function getMedia(): Collection
    {
        return $this->media;
    }

        

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDatedudebut(): ?\DateTime
    {
        return $this->datedudebut;
    }

    public function setDatedudebut(?\DateTime $datedudebut): static
    {
        $this->datedudebut = $datedudebut;

        return $this;
    }

    public function getDatedufin(): ?\DateTime
    {
        return $this->datedufin;
    }

    public function setDatedufin(?\DateTime $datedufin): static
    {
        $this->datedufin = $datedufin;

        return $this;
    }
}
