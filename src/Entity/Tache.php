<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "TACHES")]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdTache", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Mission::class, inversedBy: 'taches')]
    #[ORM\JoinColumn(name: "IdMission", referencedColumnName: "IdMission", nullable: false)]
    private ?Mission $mission = null;

    #[ORM\Column(name: "LibelleTache", type: "string", length: 200)]
    private ?string $libelleTache = null;

    #[ORM\Column(name: "Description", type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "DureeEstimee", type: "decimal", precision: 8, scale: 2, nullable: true)]
    private ?string $dureeEstimee = null;

    #[ORM\Column(name: "DateDebutPrevue", type: "date", nullable: true)]
    private ?\DateTimeInterface $dateDebutPrevue = null;

    #[ORM\Column(name: "DateFinPrevue", type: "date", nullable: true)]
    private ?\DateTimeInterface $dateFinPrevue = null;

    #[ORM\Column(name: "Priorite", type: "string", columnDefinition: "enum('basse','moyenne','haute','critique')", options: ["default" => "moyenne"])]
    private ?string $priorite = 'moyenne';

    #[ORM\Column(name: "Statut", type: "string", columnDefinition: "enum('à faire','en cours','terminée','bloquée')", options: ["default" => "à faire"])]
    private ?string $statut = 'à faire';

    #[ORM\OneToMany(mappedBy: 'tache', targetEntity: TachesCompetences::class)]
    private Collection $tachesCompetences;

    #[ORM\OneToMany(mappedBy: 'tache', targetEntity: Affectation::class)]
    private Collection $affectations;

    public function __construct()
    {
        $this->tachesCompetences = new ArrayCollection();
        $this->affectations = new ArrayCollection();
    }

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

    public function getLibelleTache(): ?string
    {
        return $this->libelleTache;
    }

    public function setLibelleTache(string $libelleTache): self
    {
        $this->libelleTache = $libelleTache;
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

    public function getDureeEstimee(): ?string
    {
        return $this->dureeEstimee;
    }

    public function setDureeEstimee(?string $dureeEstimee): self
    {
        $this->dureeEstimee = $dureeEstimee;
        return $this;
    }

    public function getDateDebutPrevue(): ?\DateTimeInterface
    {
        return $this->dateDebutPrevue;
    }

    public function setDateDebutPrevue(?\DateTimeInterface $dateDebutPrevue): self
    {
        $this->dateDebutPrevue = $dateDebutPrevue;
        return $this;
    }

    public function getDateFinPrevue(): ?\DateTimeInterface
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(?\DateTimeInterface $dateFinPrevue): self
    {
        $this->dateFinPrevue = $dateFinPrevue;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): self
    {
        $this->priorite = $priorite;
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

    public function getTachesCompetences(): Collection
    {
        return $this->tachesCompetences;
    }

    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addTachesCompetence(TachesCompetences $tachesCompetence): self
    {
        if(!$this->tachesCompetences->contains($tachesCompetence))
        {
            $this->tachesCompetences->add($tachesCompetence);
            $tachesCompetence->setTache($this);
        }
        return $this;
    }

    public function removeTachesCompetence(TachesCompetences $tachesCompetence): self
    {
        if($this->tachesCompetences->removeElement($tachesCompetence))
        {
            if($tachesCompetence->getTache() === $this)
            {
                $tachesCompetence->setTache(null);
            }
        }
        return $this;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if(!$this->affectations->contains($affectation))
        {
            $this->affectations->add($affectation);
            $affectation->setTache($this);
        }
        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if($this->affectations->removeElement($affectation))
        {
            if($affectation->getTache() === $this)
            {
                $affectation->setTache(null);
            }
        }
        return $this;
    }
}