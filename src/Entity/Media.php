<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomdufichier = null;

    #[ORM\Column(length: 255)]
    private ?string $typedufichier = null;

    #[ORM\Column(length: 255)]
    private ?string $chemindufichier = null;

    #[ORM\Column]
    private ?\DateTime $uploadat = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    private ?Actualite $actualite = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    private ?Evenement $evenement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomdufichier(): ?string
    {
        return $this->nomdufichier;
    }

    public function setNomdufichier(string $nomdufichier): static
    {
        $this->nomdufichier = $nomdufichier;

        return $this;
    }

    public function getTypedufichier(): ?string
    {
        return $this->typedufichier;
    }

    public function setTypedufichier(string $typedufichier): static
    {
        $this->typedufichier = $typedufichier;

        return $this;
    }

    public function getChemindufichier(): ?string
    {
        return $this->chemindufichier;
    }

    public function setChemindufichier(string $chemindufichier): static
    {
        $this->chemindufichier = $chemindufichier;

        return $this;
    }

    public function getUploadat(): ?\DateTime
    {
        return $this->uploadat;
    }

    public function setUploadat(\DateTime $uploadat): static
    {
        $this->uploadat = $uploadat;

        return $this;
    }

    public function getActualite(): ?Actualite
    {
        return $this->actualite;
    }

    public function setActualite(?Actualite $actualite): static
    {
        $this->actualite = $actualite;

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }
}
