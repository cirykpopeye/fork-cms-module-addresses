<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class GroupDataTransferObject
 *
 * @package \Backend\Modules\Addresses\Entity
 */
class GroupDataTransferObject
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var GroupTranslationDataTransferObject[]
     */
    public $translations;

    /**
     * @var Group
     */
    public $group;

    /**
     * @var ArrayCollection<Address>
     */
    public $addresses;

    /**
     * @var MediaGroup
     */
    public $images;
}