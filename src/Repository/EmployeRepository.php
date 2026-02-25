<?php

namespace App\Repository;
use App\Entity\Employes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employes::class);
    }

    /**
     * @return Employes[] Returns an array of Employes objects
     */
    public function findByEstResponsable(bool $estResponsable, ?string $statut = null): array
    {
        $qb = $this->createQueryBuilder('e')->where('e.estResponsable = :estResponsable')->setParameter('estResponsable', $estResponsable);
        if($statut !== null)
        {
            $qb->andWhere('e.statut = :statut')->setParameter('statut', $statut);
        }
        return $qb->orderBy('e.nomEmploye', 'ASC')->getQuery()->getResult();
    }
}