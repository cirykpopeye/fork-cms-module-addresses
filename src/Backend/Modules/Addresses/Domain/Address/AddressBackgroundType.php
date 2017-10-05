<?php
namespace Backend\Modules\Addresses\Domain\Address;

use Common\Doctrine\Type\AbstractImageType;
use Common\Doctrine\ValueObject\AbstractImage;

final class AddressBackgroundType extends AbstractImageType
{
    const NAME = 'addresses_address_background';

    /**
     * @param string $image
     * @return AbstractImage
     */
    protected function createFromString(string $image): AbstractImage
    {
        return AddressBackground::fromString($image);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
