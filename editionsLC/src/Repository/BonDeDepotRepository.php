<?php

namespace App\Repository;

use App\Entity\BonDeDepot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BonDeDepot|null find($id, $lockMode = null, $lockVersion = null)
 * @method BonDeDepot|null findOneBy(array $criteria, array $orderBy = null)
 * @method BonDeDepot[]    findAll()
 * @method BonDeDepot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonDeDepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BonDeDepot::class);
    }
    
    /**
    * @return BonDeDepot[] Returns an array of BonDeDepot objects
    */
    public function findAllByAuteur()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.livre', 'l')
            ->leftJoin('l.auteur', 'a')
            ->orderBy('a.nom', 'ASC')
            ->addOrderBy('l.titre', 'ASC')
            ->addOrderBy('b.destinataire', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return BonDeDepot[] Returns an array of BonDeDepot objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BonDeDepot
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
