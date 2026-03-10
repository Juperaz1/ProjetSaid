<?php

namespace App\Controller;

use App\Entity\Affectation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TacheController extends AbstractController
{
    #[Route('/mes-taches', name: 'app_tache_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $employe = $user->getEmploye();

        if (!$employe) {
            $this->addFlash('error', 'Aucun profil employé n\'est associé à votre compte. Vous ne pouvez pas voir les tâches.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Récupérer les affectations actives de cet employé pour les tâches non terminées
        $affectations = $entityManager->getRepository(Affectation::class)->createQueryBuilder('a')
            ->innerJoin('a.tache', 't')
            ->where('a.employe = :employe')
            ->andWhere('a.statut = :statutAffectation')
            ->andWhere('t.statut != :statutTache')
            ->setParameter('employe', $employe)
            ->setParameter('statutAffectation', 'active')
            ->setParameter('statutTache', 'terminée')
            ->getQuery()
            ->getResult();

        return $this->render('tache/index.html.twig', [
            'affectations' => $affectations,
        ]);
    }
}
