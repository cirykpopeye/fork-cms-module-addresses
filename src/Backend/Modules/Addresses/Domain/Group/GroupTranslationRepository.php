<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Common\Core\Model;
use Common\Locale;
use Doctrine\ORM\EntityRepository;

class GroupTranslationRepository extends EntityRepository
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
            ->select('COUNT(ct)')
            ->from(GroupTranslation::class, 'ct')
            ->innerJoin('ct.meta', 'm')
            ->where('m.url = :URL')
            ->andWhere('ct.locale = :locale')
            ->setParameter('URL', $url)
            ->setParameter('locale', $locale);

        if ($id !== null) {
            $query
                ->andWhere('ct.group != :group')
                ->setParameter('group', $this->getEntityManager()->getReference(Group::class, $id));
        }



        if ((int) $query->getQuery()->getSingleScalarResult() === 0) {
            return $url;
        }

        return $this->getURL(Model::addNumber($url), $locale, $id);
    }
}
