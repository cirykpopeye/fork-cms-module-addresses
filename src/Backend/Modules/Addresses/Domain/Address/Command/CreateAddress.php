<?php

namespace Backend\Modules\Addresses\Domain\Address\Command;

use Backend\Core\Language\Language;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Address\AddressDataTransferObject;
use Backend\Modules\Addresses\Domain\Address\AddressTranslationDataTransferObject;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;

final class CreateAddress extends AddressDataTransferObject
{
    /**
     * CreateAddress constructor.
     */
    public function __construct()
    {
        //-- Set working language first
        $workingLanguages = Language::getWorkingLanguages();
        $tmp = $workingLanguages[Language::getWorkingLanguage()];
        unset($workingLanguages[Language::getWorkingLanguage()]);
        $workingLanguages = array(Language::getWorkingLanguage() => $tmp) + $workingLanguages;

        // make sure we have translations ready for all working languages
        foreach (array_keys($workingLanguages) as $workingLanguage) {
            $this->translations[$workingLanguage] = new AddressTranslationDataTransferObject(
                Locale::fromString($workingLanguage)
            );
        }
        //-- Fetch current media group.
        $this->mediaGroup = MediaGroup::create(Type::fromString('image'));
    }
}