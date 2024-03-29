<?php

namespace App\Repository;

use App\Dto\TourneyDatesDto;
use App\Entity\MatchResult;
use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MatchResult>
 *
 * @method MatchResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatchResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatchResult[]    findAll()
 * @method MatchResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchResult::class);
    }

    public function save(MatchResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MatchResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function userMatches(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('result')
            ->where(':user = result.homePlayer')
            ->orWhere(':user = result.awayPlayer')
            ->setParameter('user', $user);
    }

    public function getUserMatches(User $user): Query
    {
        return $this->userMatches($user)
            ->orderBy('result.finishedAt', 'DESC')
            ->getQuery();
    }

    public function getLastMatches(User $user, int $count): array
    {
        return $this->userMatches($user)
            ->orderBy('result.finishedAt', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /** Gets tourney dates by match result. But preferred way is to get it through Challonge API */
    public function getTourneyDates(Tourney $tourney): TourneyDatesDto
    {
        $result = $this->createQueryBuilder('result')
            ->select('result.finishedAt')
            ->where('result.tourney = :tourney')
            ->setParameter('tourney', $tourney)
            ->orderBy('result.finishedAt', 'ASC')
            ->getQuery()
            ->getResult();

        /** @var \DateTimeImmutable[] $result */
        $result = array_column($result, 'finishedAt');

        $last = array_key_last($result);

        return new TourneyDatesDto(
            startDate: $result[0],
            endDate: $result[$last]
        );
    }

//    /**
//     * @return MatchResult[] Returns an array of MatchResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MatchResult
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
