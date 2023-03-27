<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 *
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

    public function save(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // - la liste complète de tous les livres, triée par ordre alphabétique de titre
    /**
     * @return Livre[] Returns an array of Livre objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }



    // - les données du livre dont l'id est `1`
    /**
     * @return Livre[] Returns an array of Livre objects
     */
    public function findById(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.id = :id')
            ->setParameter('id', 1)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    // - la liste des livres dont le titre contient le mot clé `lorem`, triée par ordre alphabétique de titre
    /**
     * @return Livre[] Returns an array of Livre objects
     */
    public function findByKeyword(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.titre LIKE :titre')
            ->setParameter('titre', "%lorem%")
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }




//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
