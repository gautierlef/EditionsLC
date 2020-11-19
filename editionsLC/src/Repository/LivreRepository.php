<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function byAuteurOrderByTitre($id)
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->orderBy('l.titre', 'ASC');
    }
    
    public function orderByTitre()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.titre', 'ASC');
    }
    
    /**
    * @return Livre[] Returns an array of Livre objects
    */
    public function findAllByAuteur()
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')
            ->orderBy('a.nom', 'ASC')
            ->addOrderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
    * @return Livre[] Returns an array of Livre objects
    */
    public function findByAuteur($id)
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->orderBy('a.nom', 'ASC')
            ->addOrderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    // /**
    //  * @return Livre[] Returns an array of Livre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
