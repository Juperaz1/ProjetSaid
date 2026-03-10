<?php
// src/Entity/Utilisateurs.php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\Table(name: "UTILISATEURS")]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdUtilisateur", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "Login", type: "string", length: 50, unique: true)]
    private ?string $login = null;

    #[ORM\Column(name: "Password", type: "string", length: 255)]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: Employes::class)]
    #[ORM\JoinColumn(name: "IdEmploye", referencedColumnName: "IdEmploye", nullable: true)]
    private ?Employes $employe = null;

    // Ajout du champ roles
    #[ORM\Column(name: "Roles", type: "json", nullable: true)]
    private array $roles = [];

    private ?string $plainPassword = null;

    private bool $agreeTerms = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    public function isAgreeTerms(): bool
    {
        return $this->agreeTerms;
    }

    public function setAgreeTerms(bool $agreeTerms): self
    {
        $this->agreeTerms = $agreeTerms;
        return $this;
    }

    public function getDisplayName(): string
    {
        if ($this->employe) {
            return $this->employe->getPrenomEmploye() . ' ' . $this->employe->getNomEmploye();
        }
        return $this->login;
    }

    public function getDisplayRole(): string
    {
        if (in_array('ROLE_RESPONSABLE', $this->getRoles())) {
            return 'Responsable';
        }
        return 'Utilisateur';
    }
}