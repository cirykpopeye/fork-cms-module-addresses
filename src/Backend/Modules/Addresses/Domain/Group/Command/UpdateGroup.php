<?php

namespace Backend\Modules\Addresses\Domain\Group\Command;

use Backend\Core\Language\Language;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\Addresses\Domain\Group\GroupDataTransferObject;
use Backend\Modules\Addresses\Domain\Group\GroupTranslation;
use Backend\Modules\Addresses\Domain\Group\GroupTranslationDataTransferObject;
use Doctrine\Common\Collections\ArrayCollection;

final class UpdateGroup extends GroupDataTransferObject
{
    /**
     * CreateGroup constructor.
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $saveForLast = array();
        /**
         * @var GroupTranslation $translation
         */
        foreach ($group->getTranslations() as $translation) {
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

            //-- Locale is not yet available for this category
            $this->translations[$workingLanguage] = new GroupTranslationDataTransferObject(
                Locale::fromString($workingLanguage),
                $group
            );
        }
        $this->group = $group;
        $this->addresses = $group->getAddresses();
    }

    /**
     * @return Group
     */
    public function getGroup() {
        return $this->group;
    }

    /**
     * @return Address|ArrayCollection
     */
    public function getAddresses() {
        return $this->addresses;
    }
}
