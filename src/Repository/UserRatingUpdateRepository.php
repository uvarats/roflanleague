<?php

namespace App\Repository;

use App\Entity\UserRatingUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRatingUpdate>
 *
 * @method UserRatingUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRatingUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRatingUpdate[]    findAll()
 * @method UserRatingUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRatingUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRatingUpdate::class);
    }

    public function save(UserRatingUpdate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserRatingUpdate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserRatingUpdate[] Returns an array of UserRatingUpdate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserRatingUpdate
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
