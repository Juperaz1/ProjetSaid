<?php

namespace App\Repository;

use App\Entity\TypeMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TypeMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeMission::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.libelleTypeMission', 'ASC')
            ->getQuery()
            ->getResult();
    }
}