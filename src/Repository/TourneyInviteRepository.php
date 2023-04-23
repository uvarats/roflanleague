<?php

namespace App\Repository;

use App\Entity\TourneyInvite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TourneyInvite>
 *
 * @method TourneyInvite|null find($id, $lockMode = null, $lockVersion = null)
 * @method TourneyInvite|null findOneBy(array $criteria, array $orderBy = null)
 * @method TourneyInvite[]    findAll()
 * @method TourneyInvite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourneyInviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TourneyInvite::class);
    }

    public function save(TourneyInvite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TourneyInvite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function deactivateLinkBySlug(string $slug)
    {
        $link = $this->findOneBy([
            'slug' => $slug
        ]);

        if ($link->isActive()) {
            $link->setIsActive(false);
        }

        $this->save($link, true);
    }

//    /**
//     * @return TourneyInvite[] Returns an array of TourneyInvite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TourneyInvite
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
