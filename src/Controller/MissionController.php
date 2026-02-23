<?php

namespace App\Controller;

use App\Repository\MissionRepository;
use App\Repository\ClientRepository;
use App\Repository\TypeMissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissionController extends AbstractController
{
    #[Route('/missions', name: 'app_missions_index')]
    #[Route('/', name: 'app_home')]
    public function index(
        MissionRepository $missionRepository, 
        ClientRepository $clientRepository,
        TypeMissionRepository $typeMissionRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $missions = $missionRepository->findAllWithRelations();
        $clients = $clientRepository->findAll();
        $types = $typeMissionRepository->findAll();
        
        $stats = [
            'total' => count($missions),
            'en_cours' => $missionRepository->countByStatus('en cours'),
            'prevues' => $missionRepository->countByStatus('prévue'),
            'terminees' => $missionRepository->countByStatus('terminée')
        ];

        return $this->render('mission/index.html.twig', [
            'missions' => $missions,
            'clients' => $clients,
            'types' => $types,
            'stats' => $stats
        ]);
    }

    #[Route('/mission/{id}', name: 'app_mission_show', requirements: ['id' => '\d+'])]
    public function show(int $id, MissionRepository $missionRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $mission = $missionRepository->findWithDetails($id);
        
        if (!$mission) {
            throw $this->createNotFoundException('Mission non trouvée');
        }

        return $this->render('mission/show.html.twig', [
            'mission' => $mission
        ]);
    }
}