<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "EMPLOYES_COMPETENCES")]
class EmployesCompetences
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Employes::class, inversedBy: 'employesCompetences')]
    #[ORM\JoinColumn(name: "IdEmploye", referencedColumnName: "IdEmploye", nullable: false)]
    private ?Employes $employe = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Competence::class, inversedBy: 'employesCompetences')]
    #[ORM\JoinColumn(name: "IdCompetence", referencedColumnName: "IdCompetence", nullable: false)]
    private ?Competence $competence = null;

    #[ORM\Column(name: "Niveau", type: "string", columnDefinition: "enum('débutant','intermédiaire','avancé','expert')")]
    private ?string $niveau = null;

    public function getEmploye(): ?Employes
    {
        return $this->employe;
    }

    public function setEmploye(?Employes $employe): self
    {
        $this->employe = $employe;
        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): self
    {
        $this->competence = $competence;
        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): self
    {
        $this->niveau = $niveau;
        return $this;
    }
}