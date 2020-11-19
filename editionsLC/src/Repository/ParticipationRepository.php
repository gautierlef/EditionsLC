<?php

namespace App\Repository;

use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Participation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participation[]    findAll()
 * @method Participation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    /**
    * @return Participation[] Returns an array of Participation objects
    */
    public function findOrderByDate()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.salon', 's')
            ->orderBy('s.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    
    /**
    * @return Participation[] Returns an array of Participation objects
    */
    public function findBySalon($id)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.salon', 's')
            ->andWhere('p.salon = :id')
            ->setParameter('id', $id)
            ->orderBy('p.auteur')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /*
    public function findOneBySomeField($value): ?Participation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
