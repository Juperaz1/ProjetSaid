<?php

namespace App\Controller;

use App\Entity\Employes;
use App\Entity\Mission;
use App\Entity\FeuilleTemps;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $employe = $user->getEmploye();
        
        // Récupérer les missions de l'employé
        $missions = [];
        if ($employe) {
            $missions = $entityManager->createQueryBuilder()
                ->select('m')
                ->from(Mission::class, 'm')
                ->leftJoin('m.taches', 't')
                ->leftJoin('t.affectations', 'a')
                ->where('a.employe = :employe')
                ->setParameter('employe', $employe)
                ->orderBy('m.dateDebut', 'DESC')
                ->getQuery()
                ->getResult();
        }
        
        // Statistiques
        $stats = [
            'missionsEnCours' => count(array_filter($missions, function($m) {
                return $m->getStatut() === 'en cours';
            })),
            'heuresMois' => $this->calculerHeuresMois($entityManager, $employe),
            'tachesTerminees' => $this->compterTachesTerminees($entityManager, $employe),
            'labelsMissions' => [],
            'dataMissions' => [],
            'labelsTemps' => [],
            'dataTemps' => []
        ];
        
        // Données pour les graphiques
        $statsMission = $this->getStatsMissions($entityManager);
        $stats['labelsMissions'] = array_keys($statsMission);
        $stats['dataMissions'] = array_values($statsMission);
        
        $statsTemps = $this->getStatsTemps($entityManager, $employe);
        $stats['labelsTemps'] = array_keys($statsTemps);
        $stats['dataTemps'] = array_values($statsTemps);
        
        // Récupérer les saisies de temps récentes
        $saisiesTemps = [];
        if ($employe) {
            $saisiesTemps = $entityManager->getRepository(FeuilleTemps::class)
                ->createQueryBuilder('f')
                ->join('f.affectation', 'a')
                ->where('a.employe = :employe')
                ->setParameter('employe', $employe)
                ->orderBy('f.dateTravail', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        }
        
        // Récupérer tous les employés actifs (utilisé par le template ?)
        $employes = $entityManager->getRepository(Employes::class)->findBy(['statut' => 'actif']);

        return $this->render('dashboard/index.html.twig', [
            'missions' => $missions,
            'employes' => $employes,
            'saisiesTemps' => $saisiesTemps,
            'stats' => $stats
        ]);
    }
    
    private function calculerHeuresMois(EntityManagerInterface $entityManager, $employe): float
    {
        $debutMois = new \DateTime('first day of this month');
        $finMois = new \DateTime('last day of this month');
        
        $result = $entityManager->getRepository(FeuilleTemps::class)
            ->createQueryBuilder('f')
            ->select('SUM(f.heuresEffectuees)')
            ->join('f.affectation', 'a')
            ->where('a.employe = :employe')
            ->andWhere('f.dateTravail BETWEEN :debut AND :fin')
            ->setParameter('employe', $employe)
            ->setParameter('debut', $debutMois)
            ->setParameter('fin', $finMois)
            ->getQuery()
            ->getSingleScalarResult();
            
        return $result ? floatval($result) : 0;
    }
    
    private function compterTachesTerminees(EntityManagerInterface $entityManager, $employe): int
    {
        if (!$employe) return 0;

        return $entityManager->createQueryBuilder()
            ->select('COUNT(t)')
            ->from(Tache::class, 't')
            ->leftJoin('t.affectations', 'a')
            ->where('a.employe = :employe')
            ->andWhere('t.statut = :statut')
            ->setParameter('employe', $employe)
            ->setParameter('statut', 'terminée')
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    private function getStatsMissions(EntityManagerInterface $entityManager): array
    {
        $result = $entityManager->createQueryBuilder()
            ->select('m.statut as statut, COUNT(m) as count')
            ->from(Mission::class, 'm')
            ->groupBy('m.statut')
            ->getQuery()
            ->getResult();
            
        $stats = [];
        foreach ($result as $row) {
            $stats[$row['statut']] = $row['count'];
        }
        
        return $stats;
    }
    
    private function getStatsTemps(EntityManagerInterface $entityManager, $employe): array
    {
        if (!$employe) return [];

        $result = $entityManager->getRepository(FeuilleTemps::class)
            ->createQueryBuilder('f')
            ->select('m.noMission as mission, SUM(f.heuresEffectuees) as heures')
            ->join('f.affectation', 'a')
            ->join('a.tache', 't')
            ->join('t.mission', 'm')
            ->where('a.employe = :employe')
            ->groupBy('m.noMission')
            ->setParameter('employe', $employe)
            ->orderBy('heures', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
            
        $stats = [];
        foreach ($result as $row) {
            $stats[$row['mission']] = floatval($row['heures']);
        }
        
        return $stats;
    }
}