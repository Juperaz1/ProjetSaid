<?php

namespace App\Entity;

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
    private ?string $nom = null;

    #[ORM\Column(name: "PrenomEmploye", type: "string", length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(name: "EmailEmploye", type: "string", length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "EstResponsable", type: "boolean", options: ["default" => false])]
    private ?bool $estResponsable = false;

    #[ORM\Column(name: "Statut", type: "string", columnDefinition: "enum('actif','inactif','congé')", options: ["default" => "actif"])]
    private ?string $statut = 'actif';

    #[ORM\Column(name: "IdSite", type: "integer", nullable: true)]
    private ?int $idSite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
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

    public function getIdSite(): ?int
    {
        return $this->idSite;
    }

    public function setIdSite(?int $idSite): self
    {
        $this->idSite = $idSite;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}