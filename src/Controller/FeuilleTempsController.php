<?php

namespace App\Controller;

use App\Entity\FeuilleTemps;
use App\Entity\Affectation;
use App\Repository\AffectationRepository;
use App\Repository\FeuilleTempsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class FeuilleTempsController extends AbstractController
{
    #[Route('/feuille-temps', name: 'app_feuille_temps_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $employe = $user->getEmploye();

        if (!$employe) {
            $this->addFlash('error', 'Aucun employé associé à votre compte.');
            return $this->redirectToRoute('app_dashboard');
        }

        $feuillesTemps = $entityManager->getRepository(FeuilleTemps::class)
            ->createQueryBuilder('f')
            ->join('f.affectation', 'a')
            ->where('a.employe = :employe')
            ->setParameter('employe', $employe)
            ->orderBy('f.dateTravail', 'DESC')
            ->getQuery()
            ->getResult();

        // Stats simples pour le moment
        $stats = [
            'total_heures' => 0,
            'heures_validees' => 0,
            'heures_brouillon' => 0
        ];

        foreach ($feuillesTemps as $f) {
            $h = (float)$f->getHeuresEffectuees();
            $stats['total_heures'] += $h;
            if ($f->getStatut() === 'validé') $stats['heures_validees'] += $h;
            elseif ($f->getStatut() === 'brouillon') $stats['heures_brouillon'] += $h;
        }

        return $this->render('feuille_temps/index.html.twig', [
            'feuillesTemps' => $feuillesTemps,
            'stats' => $stats,
            'missions' => [], // TODO: fetch missions for filter
            'filtres' => ['periode' => 'ce_mois', 'statut' => 'tous', 'mission' => '']
        ]);
    }

    #[Route('/feuille-temps/nouvelle', name: 'app_feuille_temps_nouvelle', methods: ['GET'])]
    public function nouvelle(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $employe = $user->getEmploye();

        if (!$employe) {
            $this->addFlash('error', 'Aucun employé associé à votre compte.');
            return $this->redirectToRoute('app_dashboard');
        }

        $affectations = $entityManager->getRepository(Affectation::class)
            ->findBy(['employe' => $employe, 'statut' => 'active']);

        return $this->render('feuille_temps/nouvelle.html.twig', [
            'affectations' => $affectations,
            'statutsTache' => ['à faire', 'en cours', 'terminée', 'bloquée']
        ]);
    }

    #[Route('/feuille-temps/sauvegarder', name: 'app_feuille_temps_sauvegarder', methods: ['POST'])]
    public function sauvegarder(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $employe = $user->getEmploye();

        $idAffectation = $request->request->get('idAffectation');
        $dateTravailStr = $request->request->get('dateTravail');
        $heures = $request->request->get('heures');
        $description = $request->request->get('description');
        $statut = $request->request->get('statut') ?: 'brouillon';

        $dateTravail = \DateTime::createFromFormat('Y-m-d', $dateTravailStr);
        $affectation = $entityManager->getRepository(Affectation::class)->find($idAffectation);

        if ($affectation && $affectation->getEmploye() === $employe) {
            $feuille = new FeuilleTemps();
            $feuille->setAffectation($affectation);
            $feuille->setDateTravail($dateTravail);
            $feuille->setHeuresEffectuees($heures);
            $feuille->setDescription($description);
            $feuille->setStatut($statut);
            
            if ($statut === 'validé') {
                $feuille->setDateSoumission(new \DateTime()); // Gardé pour rétrocompatibilité
                $feuille->setDateValidation(new \DateTime());
            }

            $entityManager->persist($feuille);

            // Mise à jour du statut de la tâche
            $nouveauStatutTache = $request->request->get('nouveauStatutTache');
            if ($nouveauStatutTache) {
                $tache = $affectation->getTache();
                
                $tache->setStatut($nouveauStatutTache);
                $entityManager->persist($tache);

                // Mettre à jour l'avancement de la mission parent
                $mission = $tache->getMission();
                if ($mission) {
                    $mission->mettreAJourAvancement();
                    $entityManager->persist($mission);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Temps enregistré avec succès.');
        } else {
            $this->addFlash('error', 'Affectation invalide.');
        }

        return $this->redirectToRoute('app_feuille_temps_index');
    }

    #[Route('/feuille-temps/{id}/voir', name: 'app_feuille_temps_voir')]
    public function voir(int $id, EntityManagerInterface $entityManager): Response
    {
        $feuille = $entityManager->getRepository(FeuilleTemps::class)->find($id);
        if (!$feuille || $feuille->getAffectation()->getEmploye() !== $this->getUser()->getEmploye()) {
            throw $this->createNotFoundException('Feuille non trouvée');
        }

        return $this->render('feuille_temps/voir.html.twig', [
            'feuille' => $feuille
        ]);
    }

    #[Route('/feuille-temps/{id}/editer', name: 'app_feuille_temps_editer')]
    public function editer(int $id, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('info', 'La modification des feuilles de temps sera bientôt disponible.');
        return $this->redirectToRoute('app_feuille_temps_index');
    }

    #[Route('/feuille-temps/{id}/pdf', name: 'app_feuille_temps_pdf')]
    public function pdf(int $id): Response
    {
        $this->addFlash('info', 'L\'export PDF sera bientôt disponible.');
        return $this->redirectToRoute('app_feuille_temps_index');
    }
}
