<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Entity\Client;
use App\Entity\TypeMission;
use App\Entity\Employes;
use App\Entity\Tache;
use App\Entity\TachesCompetences;
use App\Entity\Affectation;
use App\Repository\ClientRepository;
use App\Repository\TypeMissionRepository;
use App\Repository\EmployeRepository;
use App\Repository\CompetenceRepository;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MissionController extends AbstractController
{
    /**
     * Nettoie une valeur numérique (enlève les espaces, remplace la virgule par le point)
     */
    private function nettoyerNombre(?string $valeur): ?string
    {
        if ($valeur === null || trim($valeur) === '') {
            return null;
        }
        
        // Enlever tous les espaces (insécables ou non)
        $valeur = preg_replace('/\s+/', '', trim($valeur));
        
        // Remplacer la virgule par le point
        $valeur = str_replace(',', '.', $valeur);
        
        // Vérifier que c'est un nombre valide
        if (!is_numeric($valeur)) {
            return null;
        }
        
        // Formater avec 2 décimales maximum
        return number_format((float)$valeur, 2, '.', '');
    }

    /**
     * Valide qu'un nombre ne dépasse pas la limite
     */
    private function validerLimiteNombre(?string $valeur, float $max, string $nomChamp): ?string
    {
        if ($valeur === null) {
            return null;
        }
        
        $nombre = floatval($valeur);
        if ($nombre > $max) {
            $this->addFlash('error', "Le champ '$nomChamp' ne peut pas dépasser " . number_format($max, 2, ',', ' '));
            return null;
        }
        
        return $valeur;
    }

    #[Route('/mission', name: 'app_mission_index')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, MissionRepository $missionRepository, ClientRepository $clientRepository, TypeMissionRepository $typeMissionRepository): Response
    {
        $filters = [
            'client' => $request->query->get('client'),
            'type' => $request->query->get('type'),
            'statut' => $request->query->get('statut'),
            'dateDebut' => $request->query->get('dateDebut'),
            'dateFin' => $request->query->get('dateFin'),
            'search' => $request->query->get('search')
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (!empty($filters)) {
            $missions = $missionRepository->findByFilters($filters);
        } else {
            $missions = $missionRepository->findActiveMissions();
        }
        
        $stats = [
            'total' => count($missions),
            'en_cours' => 0,
            'prevues' => 0,
            'terminees' => 0,
            'en_pause' => 0,
            'annulees' => 0
        ];
        
        foreach ($missions as $mission) {
            switch ($mission->getStatut()) {
                case 'en cours':
                    $stats['en_cours']++;
                    break;
                case 'prévue':
                    $stats['prevues']++;
                    break;
                case 'terminée':
                    $stats['terminees']++;
                    break;
                case 'en pause':
                    $stats['en_pause']++;
                    break;
                case 'annulée':
                    $stats['annulees']++;
                    break;
            }
        }
        
        $clients = $clientRepository->findAll();
        $types = $typeMissionRepository->findAll();
        
        return $this->render('mission/index.html.twig', [
            'missions' => $missions,
            'stats' => $stats,
            'clients' => $clients,
            'types' => $types,
            'activeFilters' => $filters
        ]);
    }
    
    #[Route('/mission/creer', name: 'app_mission_creer')]
    #[IsGranted('ROLE_USER')]
    public function creerMission(
        Request $request, 
        EntityManagerInterface $entityManager, 
        ClientRepository $clientRepository, 
        TypeMissionRepository $typeMissionRepository, 
        EmployeRepository $employeRepository, 
        CompetenceRepository $competenceRepository
    ): Response
    {
        $clients = $clientRepository->findBy([], ['nomClient' => 'ASC']);
        $typesMission = $typeMissionRepository->findBy([], ['libelleTypeMission' => 'ASC']);
        $responsables = $employeRepository->findBy(['estResponsable' => true, 'statut' => 'actif'], ['nomEmploye' => 'ASC']);
        $competences = $competenceRepository->findAllOrdered();    
        
        if ($request->isMethod('POST')) {
            $mission = new Mission();
            
            // Génération du numéro de mission
            $annee = date('Y');
            $lastMission = $entityManager->createQueryBuilder()
                ->select('m')
                ->from(Mission::class, 'm')
                ->where('m.noMission LIKE :pattern')
                ->setParameter('pattern', "MISSION-$annee-%")
                ->orderBy('m.noMission', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            
            if ($lastMission) {
                $lastNumber = intval(substr($lastMission->getNoMission(), -3));
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }
            
            $mission->setNoMission("MISSION-$annee-$newNumber");
            
            // Client
            $clientId = $request->request->get('client');
            if ($clientId) {
                $mission->setClient($clientRepository->find($clientId));
            }
            
            // Type de mission
            $typeMissionId = $request->request->get('type_mission');
            if ($typeMissionId) {
                $mission->setTypeMission($typeMissionRepository->find($typeMissionId));
            }
            
            // Dates
            $dateDebut = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_debut'));
            if ($dateDebut) {
                $mission->setDateDebut($dateDebut);
            }
            
            $dateFin = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_fin'));
            if ($dateFin) {
                $mission->setDateFinPrevue($dateFin);
            }
            
            // Description
            $mission->setDescription($request->request->get('description'));
            
            // Budget Euro (avec nettoyage et validation)
            $budgetEuro = $this->nettoyerNombre($request->request->get('budget_euro'));
            $budgetEuro = $this->validerLimiteNombre($budgetEuro, 99999999.99, 'Budget (€)');
            if ($budgetEuro !== null) {
                $mission->setBudgetEuro($budgetEuro);
            }
            
            // Budget Heures (avec nettoyage et validation)
            $budgetHeures = $this->nettoyerNombre($request->request->get('budget_heures'));
            $budgetHeures = $this->validerLimiteNombre($budgetHeures, 999999.99, 'Budget (heures)');
            if ($budgetHeures !== null) {
                $mission->setBudgetHeures($budgetHeures);
            }
            
            // Responsable
            $idResponsable = $request->request->get('responsable');
            if (!empty($idResponsable)) {
                $mission->setResponsable($employeRepository->find($idResponsable));
            }
            
            $mission->setStatut('prévue');
            $mission->setAvancementPourcentage('0.00');
            $mission->setDateCreation(new \DateTime());
            
            $entityManager->persist($mission);
            
            // Traitement des tâches - CORRECTION ICI
            $tachesLibelles = $request->request->all('tache_libelle');
            $tachesDescriptions = $request->request->all('tache_description');
            $tachesDurees = $request->request->all('tache_duree');
            $tachesPriorites = $request->request->all('tache_priorite');
            $tachesCompetences = $request->request->all('tache_competences');
            $tachesNiveaux = $request->request->all('tache_niveau');
            
            if (!empty($tachesLibelles) && is_array($tachesLibelles)) {
                foreach ($tachesLibelles as $index => $libelle) {
                    if (!empty($libelle)) {
                        $tache = new Tache();
                        $tache->setMission($mission);
                        $tache->setLibelleTache($libelle);
                        
                        // Description - utiliser all() et vérifier l'existence
                        $tache->setDescription($tachesDescriptions[$index] ?? null);
                        
                        // Durée estimée (avec nettoyage)
                        $duree = isset($tachesDurees[$index]) ? $this->nettoyerNombre($tachesDurees[$index]) : null;
                        $duree = $this->validerLimiteNombre($duree, 999999.99, 'Durée estimée');
                        $tache->setDureeEstimee($duree);
                        
                        // Priorité
                        $tache->setPriorite($tachesPriorites[$index] ?? 'moyenne');
                        $tache->setStatut('à faire');
                        $tache->setDateDebutPrevue($mission->getDateDebut());
                        $tache->setDateFinPrevue($mission->getDateFinPrevue());
                        
                        $entityManager->persist($tache);
                        
                        // Compétences requises pour la tâche
                        if (isset($tachesCompetences[$index]) && is_array($tachesCompetences[$index])) {
                            foreach ($tachesCompetences[$index] as $compIndex => $competenceId) {
                                if (!empty($competenceId) && isset($tachesNiveaux[$index][$compIndex])) {
                                    $tacheCompetence = new TachesCompetences();
                                    $tacheCompetence->setTache($tache);
                                    $tacheCompetence->setCompetence($competenceRepository->find($competenceId));
                                    $tacheCompetence->setNiveauRequis($tachesNiveaux[$index][$compIndex]);
                                    $entityManager->persist($tacheCompetence);
                                }
                            }
                        }
                    }
                }
            }
            
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Mission créée avec succès. Numéro : ' . $mission->getNoMission());
                return $this->redirectToRoute('app_mission_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de la mission : ' . $e->getMessage());
            }
        }
        
        return $this->render('mission/creer.html.twig', [
            'clients' => $clients,
            'types_mission' => $typesMission,
            'responsables' => $responsables,
            'competences' => $competences,
            'priorites' => ['basse', 'moyenne', 'haute', 'critique'],
            'niveaux' => ['débutant', 'intermédiaire', 'avancé', 'expert']
        ]);
    }
    
    #[Route('/mission/{id}', name: 'app_mission_show')]
    #[IsGranted('ROLE_USER')]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {        
        $mission = $entityManager->getRepository(Mission::class)->find($id);        
        
        if (!$mission) {
            throw $this->createNotFoundException('La mission avec l\'id ' . $id . ' n\'existe pas.');
        }
        
        $taches = $mission->getTaches();
        
        $levelWeights = [
            'débutant' => 1,
            'intermédiaire' => 2,
            'avancé' => 3,
            'expert' => 4,
        ];

        $allActiveEmployes = $entityManager->getRepository(Employes::class)->findBy(['statut' => 'actif'], ['nomEmploye' => 'ASC']);
        $eligibleByTache = [];

        foreach ($taches as $tache) {
            $requiredCompetences = $tache->getTachesCompetences();
            
            if ($requiredCompetences->isEmpty()) {
                $eligibleByTache[$tache->getId()] = $allActiveEmployes;
                continue;
            }

            $eligible = array_filter($allActiveEmployes, function($employe) use ($requiredCompetences, $levelWeights) {
                // Créer un map des compétences de l'employé pour une recherche rapide
                $employeCompetences = [];
                foreach ($employe->getEmployesCompetences() as $ec) {
                    $employeCompetences[$ec->getCompetence()->getId()] = $levelWeights[$ec->getNiveau()] ?? 0;
                }

                // Vérifier chaque compétence requise
                foreach ($requiredCompetences as $rc) {
                    $compId = $rc->getCompetence()->getId();
                    $requiredLevel = $levelWeights[$rc->getNiveauRequis()] ?? 0;
                    
                    if (!isset($employeCompetences[$compId]) || $employeCompetences[$compId] < $requiredLevel) {
                        return false;
                    }
                }
                return true;
            });
            
            $eligibleByTache[$tache->getId()] = array_values($eligible);
        }
        
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
            'taches' => $taches,
            'eligibleByTache' => $eligibleByTache,
            'employes' => $allActiveEmployes
        ]);
    }

    #[Route('/mission/tache/{id}/assigner', name: 'app_mission_tache_assigner', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function assignerTache(int $id, Request $request, EntityManagerInterface $entityManager, EmployeRepository $employeRepository): Response
    {
        $tache = $entityManager->getRepository(Tache::class)->find($id);
        if (!$tache) {
            throw $this->createNotFoundException('Tâche non trouvée');
        }

        $idEmploye = $request->request->get('employe');
        $role = $request->request->get('role') ?: 'Intervenant';
        
        $employe = $employeRepository->find($idEmploye);
        if (!$employe) {
            $this->addFlash('error', 'Employé non trouvé');
            return $this->redirectToRoute('app_mission_show', ['id' => $tache->getMission()->getId()]);
        }

        $affectation = new Affectation();
        $affectation->setTache($tache);
        $affectation->setEmploye($employe);
        $affectation->setRoleMission($role);
        $affectation->setDateAffectation(new \DateTime());
        $affectation->setStatut('active');

        $entityManager->persist($affectation);
        $entityManager->flush();

        $this->addFlash('success', sprintf('L\'employé %s a été assigné à la tâche %s', $employe->getFullName(), $tache->getLibelleTache()));

        return $this->redirectToRoute('app_mission_show', ['id' => $tache->getMission()->getId()]);
    }


    #[Route('/mission/{id}/edit', name: 'app_mission_edit')]
    #[IsGranted('ROLE_USER')]
    public function editMission(
        Mission $mission, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        ClientRepository $clientRepository, 
        TypeMissionRepository $typeMissionRepository, 
        EmployeRepository $employeRepository, 
        CompetenceRepository $competenceRepository
    ): Response
    {
        $clients = $clientRepository->findBy([], ['nomClient' => 'ASC']);
        $typesMission = $typeMissionRepository->findBy([], ['libelleTypeMission' => 'ASC']);
        $responsables = $employeRepository->findBy(['estResponsable' => true, 'statut' => 'actif'], ['nomEmploye' => 'ASC']);
        $competences = $competenceRepository->findAllOrdered();
        
        if ($request->isMethod('POST')) {
            // Client
            $clientId = $request->request->get('client');
            if ($clientId) {
                $mission->setClient($clientRepository->find($clientId));
            }
            
            // Type de mission
            $typeMissionId = $request->request->get('type_mission');
            if ($typeMissionId) {
                $mission->setTypeMission($typeMissionRepository->find($typeMissionId));
            }
            
            // Dates
            $dateDebut = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_debut'));
            if ($dateDebut) {
                $mission->setDateDebut($dateDebut);
            }
            
            $dateFin = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_fin'));
            if ($dateFin) {
                $mission->setDateFinPrevue($dateFin);
            }
            
            $mission->setDescription($request->request->get('description'));
            
            // Budget Euro (avec nettoyage et validation)
            $budgetEuro = $this->nettoyerNombre($request->request->get('budget_euro'));
            $budgetEuro = $this->validerLimiteNombre($budgetEuro, 99999999.99, 'Budget (€)');
            $mission->setBudgetEuro($budgetEuro);
            
            // Budget Heures (avec nettoyage et validation)
            $budgetHeures = $this->nettoyerNombre($request->request->get('budget_heures'));
            $budgetHeures = $this->validerLimiteNombre($budgetHeures, 999999.99, 'Budget (heures)');
            $mission->setBudgetHeures($budgetHeures);
            
            // Responsable
            $idResponsable = $request->request->get('responsable');
            $mission->setResponsable(!empty($idResponsable) ? $employeRepository->find($idResponsable) : null);
            
            $mission->setStatut($request->request->get('statut'));
            
            // Supprimer les anciennes tâches et leurs compétences
            foreach ($mission->getTaches() as $ancienneTache) {
                foreach ($ancienneTache->getTachesCompetences() as $tc) {
                    $entityManager->remove($tc);
                }
                $entityManager->remove($ancienneTache);
            }
            
            // Créer les nouvelles tâches - CORRECTION ICI AUSSI
            $tachesLibelles = $request->request->all('tache_libelle');
            $tachesDescriptions = $request->request->all('tache_description');
            $tachesDurees = $request->request->all('tache_duree');
            $tachesPriorites = $request->request->all('tache_priorite');
            $tachesCompetences = $request->request->all('tache_competences');
            $tachesNiveaux = $request->request->all('tache_niveau');
            
            if (!empty($tachesLibelles) && is_array($tachesLibelles)) {
                foreach ($tachesLibelles as $index => $libelle) {
                    if (!empty($libelle)) {
                        $tache = new Tache();
                        $tache->setMission($mission);
                        $tache->setLibelleTache($libelle);
                        
                        // Description - utiliser all() et vérifier l'existence
                        $tache->setDescription($tachesDescriptions[$index] ?? null);
                        
                        // Durée estimée (avec nettoyage)
                        $duree = isset($tachesDurees[$index]) ? $this->nettoyerNombre($tachesDurees[$index]) : null;
                        $duree = $this->validerLimiteNombre($duree, 999999.99, 'Durée estimée');
                        $tache->setDureeEstimee($duree);
                        
                        // Priorité
                        $tache->setPriorite($tachesPriorites[$index] ?? 'moyenne');
                        $tache->setStatut('à faire');
                        $tache->setDateDebutPrevue($mission->getDateDebut());
                        $tache->setDateFinPrevue($mission->getDateFinPrevue());
                        
                        $entityManager->persist($tache);
                        
                        // Compétences requises
                        if (isset($tachesCompetences[$index]) && is_array($tachesCompetences[$index])) {
                            foreach ($tachesCompetences[$index] as $compIndex => $competenceId) {
                                if (!empty($competenceId) && isset($tachesNiveaux[$index][$compIndex])) {
                                    $tacheCompetence = new TachesCompetences();
                                    $tacheCompetence->setTache($tache);
                                    $tacheCompetence->setCompetence($competenceRepository->find($competenceId));
                                    $tacheCompetence->setNiveauRequis($tachesNiveaux[$index][$compIndex]);
                                    $entityManager->persist($tacheCompetence);
                                }
                            }
                        }
                    }
                }
            }
            
            try {
                $mission->mettreAJourAvancement();
                
                $entityManager->flush();
                $this->addFlash('success', 'Mission mise à jour avec succès.');
                return $this->redirectToRoute('app_mission_show', ['id' => $mission->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
            }
        }
        
        return $this->render('mission/edit.html.twig', [
            'mission' => $mission,
            'clients' => $clients,
            'types_mission' => $typesMission,
            'responsables' => $responsables,
            'competences' => $competences,
            'priorites' => ['basse', 'moyenne', 'haute', 'critique'],
            'niveaux' => ['débutant', 'intermédiaire', 'avancé', 'expert']
        ]);
    }

    #[Route('/mission/{id}/delete', name: 'app_mission_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteMission(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $mission = $entityManager->getRepository(Mission::class)->find($id);
        
        if (!$mission) {
            throw $this->createNotFoundException('Mission non trouvée');
        }
        
        if ($this->isCsrfTokenValid('delete' . $mission->getId(), $request->request->get('_token'))) {
            $tacheIds = [];
            foreach ($mission->getTaches() as $tache) {
                $tacheIds[] = $tache->getId();
            }
            
            if (!empty($tacheIds)) {
                $entityManager->createQueryBuilder()
                    ->delete(TachesCompetences::class, 'tc')
                    ->where('tc.tache IN (:tacheIds)')
                    ->setParameter('tacheIds', $tacheIds)
                    ->getQuery()
                    ->execute();
                
                $entityManager->createQueryBuilder()
                    ->delete(Affectation::class, 'a')
                    ->where('a.tache IN (:tacheIds)')
                    ->setParameter('tacheIds', $tacheIds)
                    ->getQuery()
                    ->execute();
            }            
            
            foreach ($mission->getTaches() as $tache) {
                $entityManager->remove($tache);
            }
            
            $entityManager->remove($mission);
            
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Mission supprimée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
        
        return $this->redirectToRoute('app_mission_index');
    }
}