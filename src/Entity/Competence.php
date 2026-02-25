<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "COMPETENCES")]
class Competence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdCompetence", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "LibelleCompetence", type: "string", length: 100, unique: true)]
    private ?string $libelleCompetence = null;

    #[ORM\OneToMany(mappedBy: 'competence', targetEntity: TachesCompetences::class)]
    private Collection $tachesCompetences;

    #[ORM\OneToMany(mappedBy: 'competence', targetEntity: EmployesCompetences::class)]
    private Collection $employesCompetences;

    public function __construct()
    {
        $this->tachesCompetences = new ArrayCollection();
        $this->employesCompetences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleCompetence(): ?string
    {
        return $this->libelleCompetence;
    }

    public function setLibelleCompetence(string $libelleCompetence): self
    {
        $this->libelleCompetence = $libelleCompetence;
        return $this;
    }

    /**
     * @return Collection<int, TachesCompetences>
     */
    public function getTachesCompetences(): Collection
    {
        return $this->tachesCompetences;
    }

    /**
     * @return Collection<int, EmployesCompetences>
     */
    public function getEmployesCompetences(): Collection
    {
        return $this->employesCompetences;
    }
}