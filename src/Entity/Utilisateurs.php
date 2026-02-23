<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[Assert\IsTrue(message: 'Vous devez accepter les conditions d\'utilisation.')]
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

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        
        if ($this->employe && $this->employe->isEstResponsable()) {
            $roles[] = 'ROLE_RESPONSABLE';
        }
        
        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
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
}