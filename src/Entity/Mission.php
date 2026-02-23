<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "MISSIONS")]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdMission", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "NoMission", type: "string", length: 20, unique: true)]
    private ?string $noMission = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "IdClient", referencedColumnName: "IdClient", nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: TypeMission::class)]
    #[ORM\JoinColumn(name: "IdTypeMission", referencedColumnName: "IdTypeMission", nullable: false)]
    private ?TypeMission $typeMission = null;

    #[ORM\Column(name: "DateDebut", type: "date")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: "DateFinPrevue", type: "date")]
    private ?\DateTimeInterface $dateFinPrevue = null;

    #[ORM\Column(name: "Description", type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "BudgetEuro", type: "decimal", precision: 10, scale: 2, nullable: true)]
    private ?string $budgetEuro = null;

    #[ORM\Column(name: "BudgetHeures", type: "decimal", precision: 8, scale: 2, nullable: true)]
    private ?string $budgetHeures = null;

    #[ORM\ManyToOne(targetEntity: Employes::class)]
    #[ORM\JoinColumn(name: "IdResponsable", referencedColumnName: "IdEmploye", nullable: true)]
    private ?Employes $responsable = null;

    #[ORM\Column(name: "Statut", type: "string", columnDefinition: "enum('prévue','en cours','en pause','terminée','annulée')", options: ["default" => "prévue"])]
    private ?string $statut = 'prévue';

    #[ORM\Column(name: "AvancementPourcentage", type: "decimal", precision: 5, scale: 2, options: ["default" => 0.00])]
    private ?string $avancementPourcentage = '0.00';

    #[ORM\Column(name: "DateCreation", type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: Affectation::class)]
    private Collection $affectations;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: Tache::class)]
    private Collection $taches;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: PlanningGantt::class)]
    private Collection $plannings;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->plannings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoMission(): ?string
    {
        return $this->noMission;
    }

    public function setNoMission(string $noMission): self
    {
        $this->noMission = $noMission;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getTypeMission(): ?TypeMission
    {
        return $this->typeMission;
    }

    public function setTypeMission(?TypeMission $typeMission): self
    {
        $this->typeMission = $typeMission;
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

    public function getDateFinPrevue(): ?\DateTimeInterface
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(\DateTimeInterface $dateFinPrevue): self
    {
        $this->dateFinPrevue = $dateFinPrevue;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getBudgetEuro(): ?string
    {
        return $this->budgetEuro;
    }

    public function setBudgetEuro(?string $budgetEuro): self
    {
        $this->budgetEuro = $budgetEuro;
        return $this;
    }

    public function getBudgetHeures(): ?string
    {
        return $this->budgetHeures;
    }

    public function setBudgetHeures(?string $budgetHeures): self
    {
        $this->budgetHeures = $budgetHeures;
        return $this;
    }

    public function getResponsable(): ?Employes
    {
        return $this->responsable;
    }

    public function setResponsable(?Employes $responsable): self
    {
        $this->responsable = $responsable;
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

    public function getAvancementPourcentage(): ?string
    {
        return $this->avancementPourcentage;
    }

    public function setAvancementPourcentage(?string $avancementPourcentage): self
    {
        $this->avancementPourcentage = $avancementPourcentage;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    /**
     * @return Collection<int, Affectation>
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations->add($affectation);
            $affectation->setMission($this);
        }
        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->removeElement($affectation)) {
            if ($affectation->getMission() === $this) {
                $affectation->setMission(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setMission($this);
        }
        return $this;
    }

    public function removeTache(Tache $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getMission() === $this) {
                $tache->setMission(null);
            }
        }
        return $this;
    }
}