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
    #[Route('/mission', name: 'app_mission_index')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, MissionRepository $missionRepository, ClientRepository $clientRepository, TypeMissionRepository $typeMissionRepository)
    :
    Response
    {
        $filters = [
            'client' => $request->query->get('client'),
            'type' => $request->query->get('type'),
            'statut' => $request->query->get('statut'),
            'dateDebut' => $request->query->get('dateDebut'),
            'dateFin' => $request->query->get('dateFin'),
            'search' => $request->query->get('search')
        ];
        $filters = array_filter($filters, function($value)
        {
            return $value !== null && $value !== '';
        });
        if(!empty($filters))
        {
            $missions = $missionRepository->findByFilters($filters);
        }
        else
        {
            $missions = $missionRepository->findBy([], ['dateCreation' => 'DESC']);
        }
        $stats = [
            'total' => count($missions),
            'en_cours' => 0,
            'prevues' => 0,
            'terminees' => 0,
            'en_pause' => 0,
            'annulees' => 0
        ];
        foreach($missions as $mission)
        {
            switch($mission->getStatut())
            {
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
    public function creerMission(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository, TypeMissionRepository $typeMissionRepository, EmployeRepository $employeRepository, CompetenceRepository $competenceRepository)
    :
    Response
    {
        $clients = $clientRepository->findBy([], ['nomClient' => 'ASC']);
        $typesMission = $typeMissionRepository->findBy([], ['libelleTypeMission' => 'ASC']);
        $responsables = $employeRepository->findBy(['estResponsable' => true, 'statut' => 'actif'], ['nomEmploye' => 'ASC']);
        $competences = $competenceRepository->findAllOrdered();    
        if($request->isMethod('POST'))
        {
            $mission = new Mission();
            $annee = date('Y');
            $lastMission = $entityManager->createQueryBuilder()->select('m')->from(Mission::class, 'm')->where('m.noMission LIKE :pattern')->setParameter('pattern', "MISSION-$annee-%")->orderBy('m.noMission', 'DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
            if($lastMission)
            {
                $lastNumber = intval(substr($lastMission->getNoMission(), -3));
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            }
            else
            {
                $newNumber = '001';
            }
            $mission->setNoMission("MISSION-$annee-$newNumber");
            $mission->setClient($clientRepository->find($request->request->get('client')));
            $mission->setTypeMission($typeMissionRepository->find($request->request->get('type_mission')));
            $dateDebut = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_debut'));
            $mission->setDateDebut($dateDebut);
            $dateFin = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_fin'));
            $mission->setDateFinPrevue($dateFin);
            $mission->setDescription($request->request->get('description'));
            $budgetEuro = $request->request->get('budget_euro');
            if(!empty($budgetEuro))
            {
                $mission->setBudgetEuro(str_replace(',', '.', $budgetEuro));
            }
            $budgetHeures = $request->request->get('budget_heures');
            if(!empty($budgetHeures))
            {
                $mission->setBudgetHeures(str_replace(',', '.', $budgetHeures));
            }
            $idResponsable = $request->request->get('responsable');
            if(!empty($idResponsable))
            {
                $mission->setResponsable($employeRepository->find($idResponsable));
            }
            $mission->setStatut('prévue');
            $mission->setAvancementPourcentage('0.00');
            $mission->setDateCreation(new \DateTime());
            $tachesLibelles = $request->request->all('tache_libelle');
            $tachesDescriptions = $request->request->all('tache_description');
            $tachesDurees = $request->request->all('tache_duree');
            $tachesPriorites = $request->request->all('tache_priorite');
            $tachesCompetences = $request->request->all('tache_competences');
            $tachesNiveaux = $request->request->all('tache_niveau');
            if(!empty($tachesLibelles))
            {
                foreach($tachesLibelles as $index => $libelle)
                {
                    if(!empty($libelle))
                    {
                        $tache = new Tache();
                        $tache->setMission($mission);
                        $tache->setLibelleTache($libelle);
                        $tache->setDescription($tachesDescriptions[$index] ?? null);
                        $tache->setDureeEstimee(!empty($tachesDurees[$index]) ? str_replace(',', '.', $tachesDurees[$index]) : null);
                        $tache->setPriorite($tachesPriorites[$index] ?? 'moyenne');
                        $tache->setStatut('à faire');
                        $tache->setDateDebutPrevue($mission->getDateDebut());
                        $tache->setDateFinPrevue($mission->getDateFinPrevue());
                        $entityManager->persist($tache);    
                        if(isset($tachesCompetences[$index]) && is_array($tachesCompetences[$index]))
                        {
                            foreach($tachesCompetences[$index] as $compIndex => $competenceId)
                            {
                                if(!empty($competenceId) && isset($tachesNiveaux[$index][$compIndex]))
                                {
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
            $entityManager->persist($mission);
            $entityManager->flush();
            $this->addFlash('success', 'Mission créée avec succès. Numéro : ' . $mission->getNoMission());
            return $this->redirectToRoute('app_mission_index');
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
        if(!$mission)
        {
            throw $this->createNotFoundException('La mission avec l\'id ' . $id . ' n\'existe pas.');
        }
        $taches = $mission->getTaches();
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
            'taches' => $taches
        ]);
    }

    #[Route('/mission/{id}/edit', name: 'app_mission_edit')]
    #[IsGranted('ROLE_USER')]
    public function editMission(Mission $mission, Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository, TypeMissionRepository $typeMissionRepository, EmployeRepository $employeRepository, CompetenceRepository $competenceRepository)
    :
    Response
    {
        $clients = $clientRepository->findBy([], ['nomClient' => 'ASC']);
        $typesMission = $typeMissionRepository->findBy([], ['libelleTypeMission' => 'ASC']);
        $responsables = $employeRepository->findBy(['estResponsable' => true, 'statut' => 'actif'], ['nomEmploye' => 'ASC']);
        $competences = $competenceRepository->findAllOrdered(); // AJOUTER CETTE LIGNE
        if($request->isMethod('POST'))
        {
            $mission->setClient($clientRepository->find($request->request->get('client')));
            $mission->setTypeMission($typeMissionRepository->find($request->request->get('type_mission')));
            $dateDebut = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_debut'));
            $mission->setDateDebut($dateDebut);
            $dateFin = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_fin'));
            $mission->setDateFinPrevue($dateFin);
            $mission->setDescription($request->request->get('description'));
            $budgetEuro = $request->request->get('budget_euro');
            $mission->setBudgetEuro(!empty($budgetEuro) ? str_replace(',', '.', $budgetEuro) : null);
            $budgetHeures = $request->request->get('budget_heures');
            $mission->setBudgetHeures(!empty($budgetHeures) ? str_replace(',', '.', $budgetHeures) : null);
            $idResponsable = $request->request->get('responsable');
            $mission->setResponsable(!empty($idResponsable) ? $employeRepository->find($idResponsable) : null);
            $mission->setStatut($request->request->get('statut'));
            foreach($mission->getTaches() as $ancienneTache)
            {
                foreach($ancienneTache->getTachesCompetences() as $tc)
                {
                    $entityManager->remove($tc);
                }
                $entityManager->remove($ancienneTache);
            }
            $tachesLibelles = $request->request->all('tache_libelle');
            $tachesDescriptions = $request->request->all('tache_description');
            $tachesDurees = $request->request->all('tache_duree');
            $tachesPriorites = $request->request->all('tache_priorite');
            $tachesCompetences = $request->request->all('tache_competences');
            $tachesNiveaux = $request->request->all('tache_niveau');
            if(!empty($tachesLibelles))
            {
                foreach($tachesLibelles as $index => $libelle)
                {
                    if(!empty($libelle))
                    {
                        $tache = new Tache();
                        $tache->setMission($mission);
                        $tache->setLibelleTache($libelle);
                        $tache->setDescription($tachesDescriptions[$index] ?? null);
                        $tache->setDureeEstimee(!empty($tachesDurees[$index]) ? str_replace(',', '.', $tachesDurees[$index]) : null);
                        $tache->setPriorite($tachesPriorites[$index] ?? 'moyenne');
                        $tache->setStatut('à faire');
                        $tache->setDateDebutPrevue($mission->getDateDebut());
                        $tache->setDateFinPrevue($mission->getDateFinPrevue());
                        $entityManager->persist($tache);
                        if(isset($tachesCompetences[$index]) && is_array($tachesCompetences[$index]))
                        {
                            foreach($tachesCompetences[$index] as $compIndex => $competenceId)
                            {
                                if(!empty($competenceId) && isset($tachesNiveaux[$index][$compIndex]))
                                {
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
            $entityManager->flush();
            $this->addFlash('success', 'Mission mise à jour avec succès.');
            return $this->redirectToRoute('app_mission_show', ['id' => $mission->getId()]);
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
    public function deleteMission(int $id, Request $request, EntityManagerInterface $entityManager)
    :
    Response
    {
        $mission = $entityManager->getRepository(Mission::class)->find($id);
        if(!$mission)
        {
            throw $this->createNotFoundException('Mission non trouvée');
        }
        if($this->isCsrfTokenValid('delete' . $mission->getId(), $request->request->get('_token')))
        {
            $tacheIds = [];
            foreach($mission->getTaches() as $tache)
            {
                $tacheIds[] = $tache->getId();
            }
            if(!empty($tacheIds))
            {
                $entityManager->createQueryBuilder()->delete(TachesCompetences::class, 'tc')->where('tc.tache IN (:tacheIds)')->setParameter('tacheIds', $tacheIds)->getQuery()->execute();
            }
            if(!empty($tacheIds))
            {
                $entityManager->createQueryBuilder()->delete(Affectation::class, 'a')->where('a.tache IN (:tacheIds)')->setParameter('tacheIds', $tacheIds)->getQuery()->execute();
            }            
            foreach($mission->getTaches() as $tache)
            {
                $entityManager->remove($tache);
            }
            $entityManager->remove($mission);
            $entityManager->flush();
            $this->addFlash('success', 'Mission supprimée avec succès.');
        }
        else
        {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
        return $this->redirectToRoute('app_mission_index');
    }
}