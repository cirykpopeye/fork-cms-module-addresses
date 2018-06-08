<?php

namespace Backend\Modules\Addresses\Domain\Address\Command;

use Backend\Modules\Addresses\Domain\Address\AddressTranslationDataTransferObject;
use Backend\Modules\Addresses\Domain\Address\AddressTranslation;
use Backend\Modules\Addresses\Exceptions\TranslationNotFoundException;
use Backend\Modules\Addresses\Domain\Address\AddressBackground;
use Backend\Modules\Addresses\Domain\Address\AddressLogo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UpdateAddressHandler
{
    /**
     * @param UpdateAddress $updateAddress
     */
    public function handle(UpdateAddress $updateAddress)
    {
        //-- Fetch item
        $address = $updateAddress->getAddress();

        $address->update(
            $updateAddress->groups,
            $updateAddress->mediaGroup,
            $updateAddress->lastName,
            $updateAddress->firstName,
            $updateAddress->city,
            $updateAddress->postal,
            $updateAddress->street,
            $updateAddress->number,
            $updateAddress->country,
            $updateAddress->telephone,
            $updateAddress->email,
            $updateAddress->fax,
            $updateAddress->website,
            $updateAddress->btw,
            $updateAddress->note
        );

        array_map(
            function (AddressTranslationDataTransferObject $addressTranslationDataTransferObject) use ($address) {
                try {
                    $translation = $address->getTranslation($addressTranslationDataTransferObject->getLocale());

                    $translation->update(
                        $addressTranslationDataTransferObject->title,
                        $addressTranslationDataTransferObject->description,
                        $addressTranslationDataTransferObject->summary,
                        $addressTranslationDataTransferObject->hidden,
                        $addressTranslationDataTransferObject->company,
                        $addressTranslationDataTransferObject->titleShort
                    );
                } catch (TranslationNotFoundException $translationNotFound) {
                    // this is added to the category automatically
                    new AddressTranslation(
                        $addressTranslationDataTransferObject->getLocale(),
                        $address,
                        $addressTranslationDataTransferObject->title,
                        $addressTranslationDataTransferObject->description,
                        $addressTranslationDataTransferObject->summary,
                        $addressTranslationDataTransferObject->hidden,
                        $addressTranslationDataTransferObject->meta,
                        $addressTranslationDataTransferObject->company,
                        $addressTranslationDataTransferObject->titleShort
                    );
                }
            },
            $updateAddress->translations
        );
    }
}
