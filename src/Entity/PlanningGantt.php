<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "PLANNING_GANTT")]
class PlanningGantt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdPlanning", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Mission::class, inversedBy: 'plannings')]
    #[ORM\JoinColumn(name: "IdMission", referencedColumnName: "IdMission", nullable: false)]
    private ?Mission $mission = null;

    #[ORM\Column(name: "DateDebut", type: "date")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: "DateFin", type: "date")]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: "CheminCritique", type: "boolean", options: ["default" => false])]
    private ?bool $cheminCritique = false;

    #[ORM\Column(name: "MargeTotale", type: "decimal", precision: 8, scale: 2, options: ["default" => 0.00])]
    private ?string $margeTotale = '0.00';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): self
    {
        $this->mission = $mission;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function isCheminCritique(): ?bool
    {
        return $this->cheminCritique;
    }

    public function setCheminCritique(bool $cheminCritique): self
    {
        $this->cheminCritique = $cheminCritique;
        return $this;
    }

    public function getMargeTotale(): ?string
    {
        return $this->margeTotale;
    }

    public function setMargeTotale(?string $margeTotale): self
    {
        $this->margeTotale = $margeTotale;
        return $this;
    }
}