<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Entity\Employes;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new Utilisateurs();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->get('email');
            $password = $form->get('plainPassword')->getData();
            $login = $form->get('login')->getData();
            
            if (empty($email)) {
                $this->addFlash('error', 'Veuillez entrer votre email.');
                return $this->redirectToRoute('app_register');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Veuillez entrer un email valide.');
                return $this->redirectToRoute('app_register');
            }
            
            if (empty($password)) {
                $this->addFlash('error', 'Veuillez entrer un mot de passe.');
                return $this->redirectToRoute('app_register');
            }
            
            if (strlen($password) < 6) {
                $this->addFlash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
                return $this->redirectToRoute('app_register');
            }
            
            if (empty($login)) {
                $this->addFlash('error', 'Veuillez entrer un nom d\'utilisateur.');
                return $this->redirectToRoute('app_register');
            }

            $existingEmploye = $entityManager->getRepository(Employes::class)
                ->findOneBy(['emailEmploye' => $email]);
            
            if ($existingEmploye) {
                $this->addFlash('error', 'Cet email est déjà utilisé par un employé existant.');
                return $this->redirectToRoute('app_register');
            }

            $existingUser = $entityManager->getRepository(Utilisateurs::class)
                ->findOneBy(['login' => $login]);
            
            if ($existingUser) {
                $this->addFlash('error', 'Ce nom d\'utilisateur est déjà pris.');
                return $this->redirectToRoute('app_register');
            }

            $employe = new Employes();
            $employe->setNomEmploye('À compléter');
            $employe->setPrenomEmploye('À compléter');
            $employe->setEmailEmploye($email);
            $employe->setStatut('actif');
            $employe->setEstResponsable(false);

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            $user->setEmploye($employe);

            $entityManager->persist($employe);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}