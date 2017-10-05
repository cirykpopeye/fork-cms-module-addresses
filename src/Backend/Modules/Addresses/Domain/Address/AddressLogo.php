<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Common\Doctrine\ValueObject\AbstractImage;

final class AddressLogo extends AbstractImage
{
    /**
     * @return string
     */
    protected function getUploadDir(): string
    {
        return 'addresses/address/logo';
    }
}
