<?php

namespace App\Repository;
use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('m')->leftJoin('m.client', 'c')->addSelect('c')->leftJoin('m.typeMission', 't')->addSelect('t')->leftJoin('m.responsable', 'r')->addSelect('r')->orderBy('m.dateDebut', 'DESC')->getQuery()->getResult();
    }

    public function findWithDetails(int $id): ?Mission
    {
        return $this->createQueryBuilder('m')->where('m.id = :id')->setParameter('id', $id)->leftJoin('m.client', 'c')->addSelect('c')->leftJoin('m.typeMission', 't')->addSelect('t')->leftJoin('m.responsable', 'r')->addSelect('r')->leftJoin('m.affectations', 'a')->addSelect('a')->leftJoin('a.employe', 'e')->addSelect('e')->leftJoin('m.taches', 'ta')->addSelect('ta')->getQuery()->getOneOrNullResult();
    }

    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('m')->select('COUNT(m.id)')->where('m.statut = :status')->setParameter('status', $status)->getQuery()->getSingleScalarResult();
    }

    /**
     * Recherche avancée avec filtres
     */
    public function findByFilters(?array $filters = []): array
    {
        $qb = $this->createQueryBuilder('m')->leftJoin('m.client', 'c')->leftJoin('m.typeMission', 't')->leftJoin('m.responsable', 'r');
        if(!empty($filters['client']))
        {
            $qb->andWhere('c.id = :client')->setParameter('client', $filters['client']);
        }
        if (!empty($filters['type']))
        {
            $qb->andWhere('t.id = :type')->setParameter('type', $filters['type']);
        }
        if(!empty($filters['statut']))
        {
            $qb->andWhere('m.statut = :statut')->setParameter('statut', $filters['statut']);
        }
        if(!empty($filters['dateDebut']))
        {
            $qb->andWhere('m.dateDebut >= :dateDebut')->setParameter('dateDebut', new \DateTime($filters['dateDebut']));
        }
        if(!empty($filters['dateFin']))
        {
            $qb->andWhere('m.dateFinPrevue <= :dateFin')->setParameter('dateFin', new \DateTime($filters['dateFin']));
        }
        if(!empty($filters['search']))
        {
            $search = '%' . $filters['search'] . '%';
            $qb->andWhere('m.noMission LIKE :search OR c.nomClient LIKE :search')->setParameter('search', $search);
        }
        return $qb->orderBy('m.dateCreation', 'DESC')->getQuery()->getResult();
    }

    public function testMethod(): array
    {
        return $this->findBy([], ['dateCreation' => 'DESC']);
    }
}