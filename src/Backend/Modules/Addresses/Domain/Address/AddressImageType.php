<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Common\Doctrine\Type\AbstractImageType;
use Common\Doctrine\ValueObject\AbstractImage;

final class AddressImageType extends AbstractImageType
{
    const NAME = 'addresses_address_image';

    /**
     * @param string $image
     * @return AddressImage
     */
    protected function createFromString(string $image): AbstractImage
    {
        return AddressImage::fromString($image);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
