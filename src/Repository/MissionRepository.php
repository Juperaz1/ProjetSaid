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
        return $this->createQueryBuilder('m')
            ->leftJoin('m.client', 'c')->addSelect('c')
            ->leftJoin('m.typeMission', 't')->addSelect('t')
            ->leftJoin('m.responsable', 'r')->addSelect('r')
            ->orderBy('m.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithDetails(int $id): ?Mission
    {
        return $this->createQueryBuilder('m')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('m.client', 'c')->addSelect('c')
            ->leftJoin('m.typeMission', 't')->addSelect('t')
            ->leftJoin('m.responsable', 'r')->addSelect('r')
            ->leftJoin('m.affectations', 'a')->addSelect('a')
            ->leftJoin('a.employe', 'e')->addSelect('e')
            ->leftJoin('m.taches', 'ta')->addSelect('ta')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.statut = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }
}