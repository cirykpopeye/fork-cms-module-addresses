<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Backend\Modules\Addresses\Exceptions\GroupNotFoundException;
use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    /**
     * @param Group $group
     */
    public function add(Group $group)
    {
        $this->getEntityManager()->persist($group);
    }

    public function addAddresses(Group $group) {
        $this->getEntityManager()->persist($group);
    }

    /**
     * @param Group $group
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Group $group)
    {
        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->flush($group);
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNextSequence()
    {
        return (int) $this->getEntityManager()->createQueryBuilder()
            ->select('(COALESCE(MAX(c.sequence), -1) + 1) AS sequence')
            ->from(Group::class, 'c')
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $url
     * @return mixed
     * @throws GroupNotFoundException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl($url)
    {
        if ($url === null) {
            throw new GroupNotFoundException();
        }

        try {
            return $this->getEntityManager()->createQueryBuilder()
                ->select('c')
                ->from(Group::class, 'c')
                ->innerJoin('c.translations', 'ct')
                ->innerJoin('ct.meta', 'm')
                ->where('m.url = :URL')
                ->setParameter('URL', $url)
                ->getQuery()
                ->setMaxResults(1)
                ->getSingleResult();
        } catch (GroupNotFoundException $noResultException) {
            throw new GroupNotFoundException();
        }
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount()
    {
        return (int) $this
            ->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
