<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Entity\Client;
use App\Entity\TypeMission;
use App\Entity\FeuilleTemps;
use App\Entity\Affectation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/missions/historique')]
class MissionHistoriqueController extends AbstractController
{
    #[Route('/', name: 'app_mission_historique', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les paramètres de filtre
        $annee = $request->query->get('annee');
        $clientId = $request->query->get('client');
        $typeId = $request->query->get('type');

        // Construire la requête pour les missions terminées
        $qb = $entityManager->createQueryBuilder()
            ->select('m')
            ->from(Mission::class, 'm')
            ->leftJoin('m.client', 'c')
            ->leftJoin('m.typeMission', 't')
            ->where('m.statut = :statut')
            ->setParameter('statut', 'terminée');

        // Filtre par année
        if ($annee) {
            $qb->andWhere('YEAR(m.dateFinPrevue) = :annee')
               ->setParameter('annee', $annee);
        }

        // Filtre par client
        if ($clientId) {
            $qb->andWhere('m.client = :clientId')
               ->setParameter('clientId', $clientId);
        }

        // Filtre par type
        if ($typeId) {
            $qb->andWhere('m.typeMission = :typeId')
               ->setParameter('typeId', $typeId);
        }

        $missions = $qb->orderBy('m.dateFinPrevue', 'DESC')
            ->getQuery()
            ->getResult();

        // Grouper les missions par année
        $missionsParAnnee = [];
        foreach ($missions as $mission) {
            $anneeMission = $mission->getDateFinPrevue()->format('Y');
            if (!isset($missionsParAnnee[$anneeMission])) {
                $missionsParAnnee[$anneeMission] = [];
            }
            
            // Calculer les heures réelles pour cette mission
            $heuresReelles = $this->calculerHeuresReelles($mission, $entityManager);
            $mission->heuresReelles = $heuresReelles;
            
            $missionsParAnnee[$anneeMission][] = $mission;
        }

        // Trier les années par ordre décroissant
        krsort($missionsParAnnee);

        // Récupérer tous les clients pour le filtre
        $clients = $entityManager->getRepository(Client::class)
            ->findBy([], ['nomClient' => 'ASC']);

        // Récupérer tous les types de missions pour le filtre
        $typesMissions = $entityManager->getRepository(TypeMission::class)
            ->findBy([], ['libelleTypeMission' => 'ASC']);

        return $this->render('mission/historique.html.twig', [
            'missionsParAnnee' => $missionsParAnnee,
            'clients' => $clients,
            'typesMissions' => $typesMissions,
        ]);
    }

    #[Route('/mission/{id}', name: 'app_mission_voir', methods: ['GET'])]
    public function voirMission(int $id, EntityManagerInterface $entityManager): Response
    {
        $mission = $entityManager->getRepository(Mission::class)->find($id);
        
        if (!$mission) {
            throw $this->createNotFoundException('Mission non trouvée');
        }

        $heuresReelles = $this->calculerHeuresReelles($mission, $entityManager);
        $affectations = $this->getAffectationsMission($mission, $entityManager);

        return $this->render('mission/voir.html.twig', [
            'mission' => $mission,
            'heuresReelles' => $heuresReelles,
            'affectations' => $affectations,
        ]);
    }

    #[Route('/mission/{id}/rapport', name: 'app_mission_rapport', methods: ['GET'])]
    public function rapportMission(int $id, EntityManagerInterface $entityManager): Response
    {
        $mission = $entityManager->getRepository(Mission::class)->find($id);
        
        if (!$mission) {
            throw $this->createNotFoundException('Mission non trouvée');
        }

        return $this->render('mission/rapport.html.twig', [
            'mission' => $mission,
        ]);
    }

    private function calculerHeuresReelles(Mission $mission, EntityManagerInterface $entityManager): float
    {
        // CORRECTION : Jointure correcte pour atteindre la mission
        $result = $entityManager->createQueryBuilder()
            ->select('SUM(f.heuresEffectuees)')
            ->from(FeuilleTemps::class, 'f')
            ->leftJoin('f.affectation', 'a')
            ->leftJoin('a.tache', 't')
            ->where('t.mission = :missionId')
            ->setParameter('missionId', $mission->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? floatval($result) : 0.0;
    }

    private function getAffectationsMission(Mission $mission, EntityManagerInterface $entityManager): array
    {
        return $entityManager->createQueryBuilder()
            ->select('a', 'e', 't')
            ->from(Affectation::class, 'a')
            ->leftJoin('a.employe', 'e')
            ->leftJoin('a.tache', 't')
            ->where('t.mission = :missionId')
            ->setParameter('missionId', $mission->getId())
            ->orderBy('a.dateAffectation', 'ASC')
            ->getQuery()
            ->getResult();
    }
}