<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "AFFECTATIONS")]
class Affectation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdAffectation", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tache::class, inversedBy: 'affectations')]
    #[ORM\JoinColumn(name: "IdTache", referencedColumnName: "IdTache", nullable: false)]
    private ?Tache $tache = null;

    #[ORM\ManyToOne(targetEntity: Employes::class, inversedBy: 'affectations')]
    #[ORM\JoinColumn(name: "IdEmploye", referencedColumnName: "IdEmploye", nullable: false)]
    private ?Employes $employe = null;

    #[ORM\Column(name: "RoleMission", type: "string", length: 100, nullable: true)]
    private ?string $roleMission = null;

    #[ORM\Column(name: "DateAffectation", type: "date")]
    private ?\DateTimeInterface $dateAffectation = null;

    #[ORM\Column(name: "DateFinAffectation", type: "date", nullable: true)]
    private ?\DateTimeInterface $dateFinAffectation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTache(): ?Tache
    {
        return $this->tache;
    }

    public function setTache(?Tache $tache): self
    {
        $this->tache = $tache;
        return $this;
    }

    public function getEmploye(): ?Employes
    {
        return $this->employe;
    }

    public function setEmploye(?Employes $employe): self
    {
        $this->employe = $employe;
        return $this;
    }

    public function getRoleMission(): ?string
    {
        return $this->roleMission;
    }

    public function setRoleMission(?string $roleMission): self
    {
        $this->roleMission = $roleMission;
        return $this;
    }

    public function getDateAffectation(): ?\DateTimeInterface
    {
        return $this->dateAffectation;
    }

    public function setDateAffectation(\DateTimeInterface $dateAffectation): self
    {
        $this->dateAffectation = $dateAffectation;
        return $this;
    }

    public function getDateFinAffectation(): ?\DateTimeInterface
    {
        return $this->dateFinAffectation;
    }

    public function setDateFinAffectation(?\DateTimeInterface $dateFinAffectation): self
    {
        $this->dateFinAffectation = $dateFinAffectation;
        return $this;
    }
}