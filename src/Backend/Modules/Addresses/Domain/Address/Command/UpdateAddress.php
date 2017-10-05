<?php

namespace Backend\Modules\Addresses\Domain\Address\Command;

use Backend\Core\Language\Language;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressTranslation;
use Backend\Modules\Addresses\Domain\Address\AddressTranslationDataTransferObject;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Backend\Modules\Addresses\Domain\Address\AddressDataTransferObject;

final class UpdateAddress extends AddressDataTransferObject
{
    /**
     * UpdateAddress constructor.
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $saveForLast = array();
        /**
         * @var AddressTranslation $translation
         */
        foreach ($address->getTranslations() as $translation) {
            if(Language::getWorkingLanguage() == $translation->getLocale()) {
                $saveForLast = $translation->getDataTransferObject();
            } else {
                $this->translations[(string) $translation->getLocale()] = $translation->getDataTransferObject();
            }
        }

        $this->translations = array(Language::getWorkingLanguage() => $saveForLast) + $this->translations;

        //-- Set working language first
        $workingLanguages = Language::getWorkingLanguages();
        $tmp = $workingLanguages[Language::getWorkingLanguage()];
        unset($workingLanguages[Language::getWorkingLanguage()]);
        $workingLanguages = array(Language::getWorkingLanguage() => $tmp) + $workingLanguages;

        //-- Translations ready for all working languages
        foreach(array_keys($workingLanguages) as $workingLanguage) {
            if(array_key_exists($workingLanguage, $this->translations)) {
                continue;
            }

            //-- Locale is not yet available for this item
            $this->translations[$workingLanguage] = new AddressTranslationDataTransferObject(
                Locale::fromString($workingLanguage),
                $address
            );
        }

        $this->categories = $address->getGroups();
        $this->mediaGroup = $address->getGroup();

        if (!$this->mediaGroup instanceof MediaGroup) {
            // Note: I'm using 'image' in this example, use what you want, ...
            $this->mediaGroup = MediaGroup::create(MediaGroupType::fromString('image'));
        }

        $this->address = $address;
        $this->groups = $address->getGroups();

        //-- Set values
        $this->firstName = $address->getFirstName();
        $this->lastName = $address->getLastName();
        $this->email = $address->getEmail();
        $this->street = $address->getStreet();
        $this->postal = $address->getPostal();
        $this->city = $address->getCity();
        $this->country = $address->getCountry();
        $this->telephone = $address->getTelephone();
        $this->fax = $address->getFax();
        $this->website = $address->getWebsite();
        $this->btw = $address->getBtw();
        $this->note = $address->getNote();
        $this->number = $address->getNumber();
        $this->sliderType = $address->getSliderType();
    }

    /**
     * @return Address
     */
    public function getAddress() {
        return $this->address;
    }
}
