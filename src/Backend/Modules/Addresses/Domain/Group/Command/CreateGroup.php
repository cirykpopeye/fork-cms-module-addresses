<?php

namespace Backend\Modules\Addresses\Domain\Group\Command;

use Backend\Core\Language\Language;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Group\GroupTranslationDataTransferObject;
use Backend\Modules\Addresses\Domain\Group\GroupDataTransferObject;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;

final class CreateGroup extends GroupDataTransferObject
{
    /**
     * CreateGroup constructor.
     */
    public function __construct()
    {
        $workingLanguages = Language::getWorkingLanguages();
        $workingLanguages = array(Language::getWorkingLanguage() => $workingLanguages[Language::getWorkingLanguage()]) + $workingLanguages;
        $workingLanguages = array_unique($workingLanguages);

        // make sure we have translations ready for all working languages
        foreach (array_keys($workingLanguages) as $workingLanguage) {
            $this->translations[$workingLanguage] = new GroupTranslationDataTransferObject(
                Locale::fromString($workingLanguage)
            );
        }

        $this->images = MediaGroup::create(Type::fromString('image'));
    }
}
