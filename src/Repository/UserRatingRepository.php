<?php

namespace App\Repository;

use App\Entity\Discipline;
use App\Entity\User;
use App\Entity\UserRating;
use App\Exception\RelationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRating>
 *
 * @method UserRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRating[]    findAll()
 * @method UserRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRating::class);
    }

    public function save(UserRating $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserRating $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserRatingInDiscipline(User $user, Discipline $discipline): ?UserRating
    {
        return $this->getDisciplineUserRatingBuilder($user, $discipline)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @throws NonUniqueResultException
     * @throws RelationException
     * @throws NoResultException
     */
    public function hasUserRatingInDiscipline(User $user, Discipline $discipline): bool
    {
        $query = $this->getDisciplineUserRatingBuilder($user, $discipline)
            ->select('count(rating.id)')
            ->getQuery();


        $count = (int)$query->getSingleScalarResult();

        if ($count > 1) {
            throw new RelationException("User can not have more than 1 rating for one discipline. Wtf?");
        }

        return $count === 1;
    }

    private function getDisciplineUserRatingBuilder(User $user, Discipline $discipline): QueryBuilder
    {
        return $this->createQueryBuilder('rating')
            ->where('rating.participant = :user')
            ->andWhere('rating.discipline = :discipline')
            ->setParameter('user', $user)
            ->setParameter('discipline', $discipline);
    }

//    /**
//     * @return UserRating[] Returns an array of UserRating objects
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

//    public function findOneBySomeField($value): ?UserRating
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
