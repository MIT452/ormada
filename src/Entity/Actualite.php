<?php

namespace App\Entity;

use App\Repository\ActualiteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;



#[ORM\Entity(repositoryClass: ActualiteRepository::class)]
class Actualite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?\DateTime $publierle = null;

    #[ORM\Column]
    private ?bool $dejapublier = null;

    // ✅ Une seule ligne dans l'entité — tout est géré automatiquement
    #[ORM\OneToMany(mappedBy: 'actualite', targetEntity: Media::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getPublierle(): ?\DateTime
    {
        return $this->publierle;
    }

    public function setPublierle(\DateTime $publierle): static
    {
        $this->publierle = $publierle;

        return $this;
    }

    public function isDejapublier(): ?bool
    {
        return $this->dejapublier;
    }

    public function setDejapublier(bool $dejapublier): static
    {
        $this->dejapublier = $dejapublier;

        return $this;
    }
}
