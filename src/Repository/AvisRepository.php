<?php

namespace App\Repository;

use App\Entity\Avis;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function averageForUser(Utilisateur $user): float
    {
        $qb = $this->createQueryBuilder('a')
            ->select('AVG(a.note) as avgNote')
            ->where('a.ratedUser = :user')
            ->setParameter('user', $user);
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result !== null ? (float) $result : 0.0;
    }

    public function averageValidatedForUser(Utilisateur $user): float
    {
        $qb = $this->createQueryBuilder('a')
            ->select('AVG(a.note) as avgNote')
            ->where('a.ratedUser = :user')
            ->andWhere('a.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', 'valide');
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result !== null ? (float) $result : 0.0;
    }
}
