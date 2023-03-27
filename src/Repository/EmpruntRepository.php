<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 *
 * @method Emprunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunt[]    findAll()
 * @method Emprunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function save(Emprunt $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Emprunt $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    // - la liste des 3 derniers emprunts au niveau chronologique, triée par ordre **décroissant** de date d'emprunt (le plus récent en premier)
    /**
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findLast(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.dateEmprunt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }


    // - la liste des emprunts de l'emprunteur dont l'id est `2`, triée par ordre **croissant** de date d'emprunt (le plus ancien en premier)
    /**
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findByBorrowerId(): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.emprunteur', 'em')
            ->andWhere('em.id = :id')
            ->setParameter('id', 2)
            ->orderBy('e.dateEmprunt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    

    // - la liste des emprunts du livre dont l'id est `3`, triée par ordre **décroissant** de date d'emprunt (le plus récent en premier)
    /**
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findByBookId(): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.livre', 'l')
            ->andWhere('l.id = :id')
            ->setParameter('id', 3)
            ->orderBy('e.dateEmprunt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    // - la liste des emprunts qui n'ont pas encore été retournés (c-à-d dont la date de retour est nulle), triée par ordre **croissant** de date d'emprunt (le plus ancien en premier)
    /**
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findNotReturn(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateRetour IS NULL')
            ->orderBy('e.dateEmprunt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }





//    /**
//     * @return Emprunt[] Returns an array of Emprunt objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Emprunt
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
