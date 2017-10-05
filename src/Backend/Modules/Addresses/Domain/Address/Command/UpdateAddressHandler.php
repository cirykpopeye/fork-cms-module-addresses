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

        $image = $address->getImage();
        if($updateAddress->image instanceof UploadedFile) {
            $image = $image->setFile($updateAddress->image);
        }

        $logo = $address->getLogo();
        if($updateAddress->logo instanceof UploadedFile) {
            $logo = AddressLogo::fromUploadedFile($updateAddress->logo);
        }

        $background = $address->getBackground();
        if($updateAddress->background instanceof UploadedFile) {
            $background = AddressBackground::fromUploadedFile($updateAddress->background);
        }

        $address->update(
            $image,
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
            $updateAddress->note,
            $logo,
            $background,
            $updateAddress->sliderType
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
                        $addressTranslationDataTransferObject->actionMessage,
                        $addressTranslationDataTransferObject->actionFrom,
                        $addressTranslationDataTransferObject->actionTill,
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
                        $addressTranslationDataTransferObject->actionMessage,
                        $addressTranslationDataTransferObject->actionFrom,
                        $addressTranslationDataTransferObject->actionTill,
                        $addressTranslationDataTransferObject->titleShort
                    );
                }
            },
            $updateAddress->translations
        );
    }
}
