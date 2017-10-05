<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AddressDataTransferObject
 *
 * @package \Backend\Modules\Addresses\Entity
 */
class AddressDataTransferObject
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
     * @var AddressTranslationDataTransferObject[]
     */
    public $translations;

    /**
     * @var Group|ArrayCollection
     */
    public $groups;

    /**
     * @var Address
     */
    public $address;

    /**
     * @var MediaGroup
     */
    public $mediaGroup;

    public $logo;

    public $background;

    public $sliderType;


    /**
     * @var string $firstName
     * @var string $lastName
     * @var string $email
     * @var string $street
     * @var string $postal
     * @var string $city
     * @var string $country
     * @var string $telephone
     * @var string $fax
     * @var string $website
     * @var string $btw
     * @var string $note
     */
    public $firstName, $lastName, $email, $street, $number, $postal, $city, $country, $telephone, $fax, $website, $btw, $note;
}