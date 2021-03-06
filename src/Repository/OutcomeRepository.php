<?php

namespace App\Repository;

use App\Entity\Outcome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Outcome|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outcome|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outcome[]    findAll()
 * @method Outcome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutcomeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Outcome::class);
    }

}
