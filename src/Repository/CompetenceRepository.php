<?php

namespace App\Repository;
use App\Entity\Competence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competence::class);
    }

    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('c')->orderBy('c.libelleCompetence', 'ASC')->getQuery()->getResult();
    }
}