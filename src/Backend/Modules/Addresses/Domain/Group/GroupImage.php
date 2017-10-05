<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Common\Doctrine\ValueObject\AbstractImage;

final class GroupImage extends AbstractImage
{
    /**
     * @return string
     */
    protected function getUploadDir(): string
    {
        return 'addresses/group';
    }
}
