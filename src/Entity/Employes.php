<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "EMPLOYES")]
class Employes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdEmploye", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "NomEmploye", type: "string", length: 50)]
    private ?string $nomEmploye = null;

    #[ORM\Column(name: "PrenomEmploye", type: "string", length: 50)]
    private ?string $prenomEmploye = null;

    #[ORM\Column(name: "EmailEmploye", type: "string", length: 100, unique: true)]
    private ?string $emailEmploye = null;

    #[ORM\Column(name: "EstResponsable", type: "boolean", options: ["default" => false])]
    private ?bool $estResponsable = false;

    #[ORM\Column(name: "Statut", type: "string", columnDefinition: "enum('actif','inactif','congé')", options: ["default" => "actif"])]
    private ?string $statut = 'actif';

    #[ORM\ManyToOne(targetEntity: Sites::class, inversedBy: 'employes')]
    #[ORM\JoinColumn(name: "IdSite", referencedColumnName: "IdSite")]
    private ?Sites $site = null;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Affectation::class)]
    private Collection $affectations;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: EmployesCompetences::class)]
    private Collection $employesCompetences;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
        $this->employesCompetences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEmploye(): ?string
    {
        return $this->nomEmploye;
    }

    public function setNomEmploye(string $nomEmploye): self
    {
        $this->nomEmploye = $nomEmploye;
        return $this;
    }

    public function getPrenomEmploye(): ?string
    {
        return $this->prenomEmploye;
    }

    public function setPrenomEmploye(string $prenomEmploye): self
    {
        $this->prenomEmploye = $prenomEmploye;
        return $this;
    }

    public function getEmailEmploye(): ?string
    {
        return $this->emailEmploye;
    }

    public function setEmailEmploye(string $emailEmploye): self
    {
        $this->emailEmploye = $emailEmploye;
        return $this;
    }

    public function isEstResponsable(): ?bool
    {
        return $this->estResponsable;
    }

    public function setEstResponsable(bool $estResponsable): self
    {
        $this->estResponsable = $estResponsable;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getSite(): ?Sites
    {
        return $this->site;
    }

    public function setSite(?Sites $site): self
    {
        $this->site = $site;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->prenomEmploye . ' ' . $this->nomEmploye;
    }

    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function getEmployesCompetences(): Collection
    {
        return $this->employesCompetences;
    }
}