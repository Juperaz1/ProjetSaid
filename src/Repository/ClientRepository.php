<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.nomClient', 'ASC')
            ->getQuery()
            ->getResult();
    }
}