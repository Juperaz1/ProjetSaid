<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "CLIENTS")]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdClient", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "NomClient", type: "string", length: 150)]
    private ?string $nomClient = null;

    #[ORM\Column(name: "SiretClient", type: "string", length: 14, unique: true, nullable: true)]
    private ?string $siretClient = null;

    #[ORM\Column(name: "EmailClient", type: "string", length: 100, nullable: true)]
    private ?string $emailClient = null;

    #[ORM\Column(name: "TelephoneClient", type: "string", length: 20, nullable: true)]
    private ?string $telephoneClient = null;

    #[ORM\Column(name: "AdresseClient", type: "text", nullable: true)]
    private ?string $adresseClient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): self
    {
        $this->nomClient = $nomClient;
        return $this;
    }

    public function getSiretClient(): ?string
    {
        return $this->siretClient;
    }

    public function setSiretClient(?string $siretClient): self
    {
        $this->siretClient = $siretClient;
        return $this;
    }

    public function getEmailClient(): ?string
    {
        return $this->emailClient;
    }

    public function setEmailClient(?string $emailClient): self
    {
        $this->emailClient = $emailClient;
        return $this;
    }

    public function getTelephoneClient(): ?string
    {
        return $this->telephoneClient;
    }

    public function setTelephoneClient(?string $telephoneClient): self
    {
        $this->telephoneClient = $telephoneClient;
        return $this;
    }

    public function getAdresseClient(): ?string
    {
        return $this->adresseClient;
    }

    public function setAdresseClient(?string $adresseClient): self
    {
        $this->adresseClient = $adresseClient;
        return $this;
    }
}