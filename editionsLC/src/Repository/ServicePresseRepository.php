<?php

namespace App\Repository;

use App\Entity\ServicePresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ServicePresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServicePresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServicePresse[]    findAll()
 * @method ServicePresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServicePresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServicePresse::class);
    }

    /**
    * @return ServicePresse[] Returns an array of ServicePresse objects
    */
    public function findAllByAuteur()
    {
        return $this->createQueryBuilder('sp')
            ->leftJoin('sp.livre', 'l')
            ->leftJoin('l.auteur', 'a')
            ->orderBy('a.nom', 'ASC')
            ->addOrderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    // /**
    //  * @return ServicePresse[] Returns an array of ServicePresse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ServicePresse
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
