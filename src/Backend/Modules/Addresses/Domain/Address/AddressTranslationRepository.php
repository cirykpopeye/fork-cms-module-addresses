<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Common\Core\Model;
use Common\Locale;
use Doctrine\ORM\EntityRepository;

class AddressTranslationRepository extends EntityRepository
{
    /**
     * @param $url
     * @param Locale $locale
     * @param null $id
     * @return string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function getURL($url, Locale $locale, $id = null)
    {
        $url = (string) $url;

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(it)')
            ->from(AddressTranslation::class, 'it')
            ->innerJoin('it.meta', 'm')
            ->where('m.url = :URL')
            ->andWhere('it.locale = :locale')
            ->setParameter('URL', $url)
            ->setParameter('locale', $locale);

        if ($id !== null) {
            $query
                ->andWhere('it.address != :address')
                ->setParameter('address', $this->getEntityManager()->getReference(Address::class, $id));
        }

        if ((int) $query->getQuery()->getSingleScalarResult() === 0) {
            return $url;
        }

        return $this->getURL(Model::addNumber($url), $locale, $id);
    }
}
