<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Common\Doctrine\Type\AbstractImageType;
use Common\Doctrine\ValueObject\AbstractImage;

final class GroupImageType extends AbstractImageType
{
    const NAME = 'addresses_group_image';

    /**
     * @param string $image
     * @return AbstractImage
     */
    protected function createFromString(string $image): AbstractImage
    {
        return GroupImage::fromString($image);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
