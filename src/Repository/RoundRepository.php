<?php

namespace App\Repository;

use App\Entity\Round;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Round|null find($id, $lockMode = null, $lockVersion = null)
 * @method Round|null findOneBy(array $criteria, array $orderBy = null)
 * @method Round[]    findAll()
 * @method Round[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoundRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Round::class);
    }

    /**
     * @return Round|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest(): ?Round
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
