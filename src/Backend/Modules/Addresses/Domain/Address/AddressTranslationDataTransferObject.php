<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Common\Doctrine\Entity\Meta;
use Common\Locale;
use Symfony\Component\Validator\Constraints as Assert;

final class AddressTranslationDataTransferObject
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var Address|null
     */
    private $address;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "lbl.FieldIsRequired")
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $summary;

    /**
     * @var boolean
     */
    public $hidden;

    /**
     * @var Meta
     */
    public $meta;

    /**
     * @var string $company
     */
    public $company;

    /**
     * @var \DateTime $actionTill
     */
    public $actionTill;

    /**
     * @var \DateTime $actionFrom
     */
    public $actionFrom;

    /**
     * @var \DateTime $actionMessage
     */
    public $actionMessage;

    /**
     * @var string $titleShort
     */
    public $titleShort;

    /**
     * @param Locale $locale
     * @param Address|null $address
     */
    public function __construct(Locale $locale, Address $address = null)
    {
        $this->locale = $locale;
        $this->address = $address;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}