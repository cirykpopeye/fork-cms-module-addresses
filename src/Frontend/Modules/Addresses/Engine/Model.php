<?php
namespace Frontend\Modules\Addresses\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Entity\Address;
use Backend\Modules\Addresses\Repository\AddressRepository;
use Backend\Modules\Catalog\Entity\Product;
use Backend\Modules\Catalog\Repository\ProductRepository;
use Frontend\Core\Language\Locale;

/**
 * Class Engine
 *
 * @package \Frontend\Modules\Addresses\Engine
 */
class Model
{
    public static function search(array $ids)
    {
        /** @var AddressRepository $addressRepository */
        $addressRepository = BackendModel::get('addresses.repository.address');
        $addresses = $addressRepository->findByIds($ids);

        $resultsArray = array();

        /** @var Address $address */
        foreach ($addresses as $address) {
            $resultsArray[$address->getId()] = array(
                'id' => $address->getId(),
                'title' => $address->getTranslation(Locale::frontendLanguage())->getTitle(),
                'introduction' => $address->getTranslation(Locale::frontendLanguage())->getDescription(),
                'full_url' => $address->getWebUrl()
            );
        }

        return $resultsArray;
    }
}