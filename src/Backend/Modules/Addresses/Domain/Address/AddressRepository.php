<?php

namespace Backend\Modules\Addresses\Domain\Address;
use Doctrine\ORM\EntityRepository;
use Frontend\Core\Language\Locale;

class AddressRepository extends EntityRepository
{
    /**
     * @param $ids
     * @return array
     */
    public function findByIds($ids) {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from(Address::class, 'a')
            ->where('a.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Address $address
     */
    public function add(Address $address)
    {
        $this->getEntityManager()->persist($address);
    }

    /**
     * @param Address $address
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Address $address)
    {
        $this->getEntityManager()->remove($address);
        $this->getEntityManager()->flush($address);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getOwnEntityManager() {
        return $this->getEntityManager();
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNextSequence()
    {
        return (int) $this->getEntityManager()->createQueryBuilder()
            ->select('(COALESCE(MAX(i.sequence), -1) + 1) AS sequence')
            ->from(Address::class, 'i')
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $url
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl($url) {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from(Address::class, 'a')
            ->innerJoin('a.translations', 'at')
            ->innerJoin('at.meta', 'm')
            ->where('m.url = :URL')
            ->setParameter('URL', $url)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount()
    {
        return (int) $this
            ->createQueryBuilder('i')
            ->select('count(i.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $street
     * @param $number
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByAddress($street, $number) {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from(Address::class, 'a')
            ->where('a.street = :street AND a.number = :number')
            ->setParameters(array('street' => $street, 'number' => $number))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param bool $sortBySequence
     * @return array
     */
    public function findAllAlphabetic($sortBySequence = false) {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from(Address::class, 'a')
            ->innerJoin('a.translations', 'at');

        if ($sortBySequence) {
            $qb->addOrderBy('a.sequence', 'ASC');
        } else {
            $qb->addOrderBy('at.title', 'ASC');
        }
        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     * @throws \Backend\Modules\Addresses\Exceptions\AddressNotFoundException
     */
    public function findAllForMap() {
        $sortBy = array();
        if (true) {
            $sortBy['sequence'] = 'ASC';
        }
        $addresses = parent::findBy(array(), array('sequence' => 'ASC'));
        $formattedAddresses = array();
        /**
         * @var Address $address
         */
        foreach($addresses as $address) {
            $formattedAddress = [
                'id' => $address->getId(),
                'lat' => $address->getLat(),
                'lng' => $address->getLng(),
                'title' => $address->getTranslation(Locale::frontendLanguage())->getTitle(),
                'url' => $address->getWebUrl(),
                'street' => $address->getStreet(),
                'number' => $address->getNumber(),
                'city' => $address->getCity(),
                'postal' => $address->getPostal()
            ];
            $formattedAddresses[] = $formattedAddress;
        }
        return $formattedAddresses;
    }
}
