<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "TACHES_COMPETENCES")]
class TachesCompetences
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Tache::class, inversedBy: 'tachesCompetences')]
    #[ORM\JoinColumn(name: "IdTache", referencedColumnName: "IdTache", nullable: false)]
    private ?Tache $tache = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Competence::class, inversedBy: 'tachesCompetences')]
    #[ORM\JoinColumn(name: "IdCompetence", referencedColumnName: "IdCompetence", nullable: false)]
    private ?Competence $competence = null;

    #[ORM\Column(name: "NiveauRequis", type: "string", columnDefinition: "enum('débutant','intermédiaire','avancé','expert')")]
    private ?string $niveauRequis = null;

    public function getTache(): ?Tache
    {
        return $this->tache;
    }

    public function setTache(?Tache $tache): self
    {
        $this->tache = $tache;
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

    public function getNiveauRequis(): ?string
    {
        return $this->niveauRequis;
    }

    public function setNiveauRequis(string $niveauRequis): self
    {
        $this->niveauRequis = $niveauRequis;
        return $this;
    }
}