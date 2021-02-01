<?php

namespace UserApi\Repository;

use UserApi\Entity\User;
use UserApi\Enum\CacheEnum;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    public function getUsers(string $filter) : array
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->where($qb->expr()->like('u.firstName', ':name'))
            ->orderBy('u.firstName')
            ->setParameter('name', '%' . $filter . '%')
            ->getQuery()
            ->enableResultCache(
                CacheEnum::DOCTRINE_CACHE_LIFETIME,
                CacheEnum::DOCTRINE_USER_CREATED_CACHE_ID
            )->getResult();
    }

    /**
     * @param User $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveUser(User $user) : void
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
        $this->_em->getConfiguration()->getResultCacheImpl()->delete(CacheEnum::DOCTRINE_USER_CREATED_CACHE_ID);
    }
}
