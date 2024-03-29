<?php

namespace App\Repository;

use App\Entity\Tourney;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @ORM\Embeddable
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    public function getCount(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getCountVerifiedAndNotBanned() {
        return $this->createQueryBuilder('user')
            ->select('count(user.id)')
            ->where('user.isBanned = false')
            ->andWhere('user.isVerified = true')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getListByPage(int $page = 1, int $resultsPerPage = 30) {
        $em = $this->getEntityManager();
        $firstResult = ($page - 1) * $resultsPerPage;
        return $em->createQuery('SELECT u FROM App\Entity\User u')
            ->setFirstResult($firstResult)
            ->setMaxResults($resultsPerPage)
            ->getResult();
    }

    public function getUsersNotInTourney(Tourney $tourney, int $page = 1, int $resultsPerPage = 30) {
        $em = $this->getEntityManager();
        $firstResult = ($page - 1) * $resultsPerPage;
        return $this->createQueryBuilder("user")
            ->where(':tourney NOT MEMBER OF user.tourneys')
            ->setParameter('tourney', $tourney)
            ->setFirstResult($firstResult)
            ->setMaxResults($resultsPerPage)
            ->getQuery()
            ->getResult();
    }

    public function getRatingTop(int $page = 1, int $resultsPerPage = 50) : array
    {
        $firstResult = ($page - 1) * $resultsPerPage;
        return $this->createQueryBuilder("user")
            ->select('user.id', 'user.username', 'user.rating')
            ->where('user.isVerified = true')
            ->andWhere('user.isBanned = false')
            ->orderBy('user.rating', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($resultsPerPage)
            ->getQuery()
            ->getResult();
    }

    public function getTopPosition(User $user) {
        return $this->createQueryBuilder('u')
            ->select('count(u)')
            ->where('u.isBanned = false')
            ->andWhere('u.isVerified = true')
            ->andWhere('u.rating >= :userRating')
            ->setParameter('userRating', $user->getRating())
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getFullUser(int $id): User
    {
        return $this->createQueryBuilder('user')
            ->leftJoin('user.badges', 'badges')
            ->addSelect('badges')
            ->leftJoin('user.tourneys', 'tourneys')
            ->addSelect('tourneys')
            ->leftJoin('user.challongeToken', 'challongeToken')
            ->addSelect('challongeToken')
            ->where('user.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

//    /**
//     * @return User[] Returns an array of User objects
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

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
