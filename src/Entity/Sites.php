<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "SITES")]
class Sites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdSite", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "VilleSite", type: "string", length: 100)]
    private ?string $villeSite = null;

    #[ORM\Column(name: "AdresseSite", type: "text", nullable: true)]
    private ?string $adresseSite = null;

    #[ORM\OneToMany(mappedBy: 'site', targetEntity: Employes::class)]
    private Collection $employes;

    public function __construct()
    {
        $this->employes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleSite(): ?string
    {
        return $this->villeSite;
    }

    public function setVilleSite(string $villeSite): self
    {
        $this->villeSite = $villeSite;
        return $this;
    }

    public function getAdresseSite(): ?string
    {
        return $this->adresseSite;
    }

    public function setAdresseSite(?string $adresseSite): self
    {
        $this->adresseSite = $adresseSite;
        return $this;
    }

    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function __toString(): string
    {
        return $this->villeSite ?? 'Site';
    }
}
