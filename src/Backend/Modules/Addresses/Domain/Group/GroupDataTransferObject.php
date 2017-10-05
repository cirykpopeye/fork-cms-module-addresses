<?php

namespace Backend\Modules\Addresses\Domain\Group;

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
     * @var UploadedFile
     *
     * @Assert\File(
     *     maxSize = "6M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png"},
     *     mimeTypesMessage = "err.JPGGIFAndPNGOnly"
     * )
     */
    public $image;

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
}