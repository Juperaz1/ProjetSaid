<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "FEUILLES_TEMPS")]
class FeuilleTemps
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdFeuilleTemps", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Affectation::class, inversedBy: 'feuillesTemps')]
    #[ORM\JoinColumn(name: "IdAffectation", referencedColumnName: "IdAffectation", nullable: false)]
    private ?Affectation $affectation = null;

    #[ORM\Column(name: "DateTravail", type: "date")]
    private ?\DateTimeInterface $dateTravail = null;

    #[ORM\Column(name: "HeuresEffectuees", type: "decimal", precision: 5, scale: 2)]
    private ?string $heuresEffectuees = null;

    #[ORM\Column(name: "Description", type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "Statut", type: "string", columnDefinition: "enum('brouillon','validé','rejeté')", options: ["default" => "brouillon"])]
    private ?string $statut = 'brouillon';

    #[ORM\Column(name: "DateSoumission", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateSoumission = null;

    #[ORM\Column(name: "DateValidation", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(name: "CommentaireRejet", type: "text", nullable: true)]
    private ?string $commentaireRejet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAffectation(): ?Affectation
    {
        return $this->affectation;
    }

    public function setAffectation(?Affectation $affectation): self
    {
        $this->affectation = $affectation;
        return $this;
    }

    public function getDateTravail(): ?\DateTimeInterface
    {
        return $this->dateTravail;
    }

    public function setDateTravail(\DateTimeInterface $dateTravail): self
    {
        $this->dateTravail = $dateTravail;
        return $this;
    }

    public function getHeuresEffectuees(): ?string
    {
        return $this->heuresEffectuees;
    }

    public function setHeuresEffectuees(string $heuresEffectuees): self
    {
        $this->heuresEffectuees = $heuresEffectuees;
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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateSoumission(): ?\DateTimeInterface
    {
        return $this->dateSoumission;
    }

    public function setDateSoumission(?\DateTimeInterface $dateSoumission): self
    {
        $this->dateSoumission = $dateSoumission;
        return $this;
    }

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeInterface $dateValidation): self
    {
        $this->dateValidation = $dateValidation;
        return $this;
    }

    public function getCommentaireRejet(): ?string
    {
        return $this->commentaireRejet;
    }

    public function setCommentaireRejet(?string $commentaireRejet): self
    {
        $this->commentaireRejet = $commentaireRejet;
        return $this;
    }
}
