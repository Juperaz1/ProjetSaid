<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\RegistrationFormType;
use App\Repository\EmployesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        EmployesRepository $employesRepository
    ): Response
    {
        $user = new Utilisateurs();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = [];
            
            // Validation manuelle
            $login = $form->get('login')->getData();
            if (empty($login)) {
                $errors[] = 'Le nom d\'utilisateur est obligatoire';
            } elseif (strlen($login) < 3) {
                $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères';
            }
            
            $password = $form->get('plainPassword')->getData();
            if (empty($password)) {
                $errors[] = 'Le mot de passe est obligatoire';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
            }
            
            $agreeTerms = $form->get('agreeTerms')->getData();
            if (!$agreeTerms) {
                $errors[] = 'Vous devez accepter les conditions d\'utilisation';
            }
            
            // Vérifier si le login existe déjà
            $existingUser = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['login' => $login]);
            if ($existingUser) {
                $errors[] = 'Ce nom d\'utilisateur est déjà pris';
            }
            
            // Essayer de lier à un employé existant (par exemple avec le même login)
            $employe = $employesRepository->findOneBy(['login' => $login]); // Si vous avez un champ login dans Employes
            // Ou si vous voulez chercher par email, vous devrez ajouter un champ email dans le formulaire
            
            if (empty($errors)) {
                // Encoder le mot de passe
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $password
                    )
                );
                
                // Lier à l'employé si trouvé
                if ($employe) {
                    $user->setEmploye($employe);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
                
                return $this->redirectToRoute('app_login');
            } else {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
            }
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}