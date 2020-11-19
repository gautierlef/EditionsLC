<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Vente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vente[]    findAll()
 * @method Vente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    /**
    * @return Vente[] Returns an array of Vente objects
    */
    public function findAllByAuteur()
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.livre', 'l')
            ->leftJoin('l.auteur', 'a')
            ->orderBy('a.nom', 'ASC')
            ->addOrderBy('l.titre', 'ASC')
            ->addOrderBy('v.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
    * @return Vente[] Returns an array of Vente objects
    */
    public function findByAuteur($value)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.livre', 'l')
            ->leftJoin('l.auteur', 'a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $value)
            ->orderBy('v.date', 'DESC')
            ->addOrderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
    * @return Vente[] Returns an array of Vente objects
    */
    public function findSum($value)
    {
        return $this->createQueryBuilder('v')
            ->select('v.date', 'l.titre', 'sum(v.nbVentes) as nbVentes')
            ->leftJoin('v.livre', 'l')
            ->leftJoin('l.auteur', 'a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $value)
            ->orderBy('v.date', 'ASC')
            ->groupBy('v.date', 'l.titre')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
    * @return Vente[] Returns an array of Vente objects
    */
    public function findSumNbVentes($value)
    {
        return $this->createQueryBuilder('v')
            ->select('l.titre', 'sum(v.nbVentes) as nbVentes')
            ->leftJoin('v.livre', 'l')
            ->leftJoin('l.auteur', 'a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $value)
            ->orderBy('l.titre', 'ASC')
            ->groupBy('l.titre')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Vente
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
