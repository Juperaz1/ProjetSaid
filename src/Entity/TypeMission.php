<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "TYPESMISSIONS")]
class TypeMission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdTypeMission", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "LibelleTypeMission", type: "string", length: 50)]
    private ?string $libelleTypeMission = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleTypeMission(): ?string
    {
        return $this->libelleTypeMission;
    }

    public function setLibelleTypeMission(string $libelleTypeMission): self
    {
        $this->libelleTypeMission = $libelleTypeMission;
        return $this;
    }
}