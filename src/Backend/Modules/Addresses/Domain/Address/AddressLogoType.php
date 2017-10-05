<?php

namespace Backend\Modules\Addresses\Domain\Address;
use Common\Doctrine\Type\AbstractImageType;
use Common\Doctrine\ValueObject\AbstractImage;

final class AddressLogoType extends AbstractImageType
{
    const NAME = 'addresses_address_logo';

    /**
     * @param string $image
     * @return AbstractImage
     */
    protected function createFromString(string $image): AbstractImage
    {
        return AddressLogo::fromString($image);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
