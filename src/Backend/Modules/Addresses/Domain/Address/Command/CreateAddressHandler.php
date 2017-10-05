<?php

namespace Backend\Modules\Addresses\Domain\Address\Command;

use Backend\Core\Engine\Authentication;
use Backend\Modules\Addresses\Domain\Address\AddressTranslationDataTransferObject;
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressTranslation;
use Backend\Modules\Addresses\Domain\Address\AddressRepository;
use Backend\Modules\Addresses\Domain\Address\AddressBackground;
use Backend\Modules\Addresses\Domain\Address\AddressImage;
use Backend\Modules\Addresses\Domain\Address\AddressLogo;

final class CreateAddressHandler
{
    /** @var AddressRepository */
    private $addressRepository;

    /**
     * @param AddressRepository $addressRepository
     */
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param CreateAddress $createAddress
     */
    public function handle(CreateAddress $createAddress)
    {
        //-- Create new address
        $createAddress->address = new Address(
            Authentication::getUser()->getUserId(),
            AddressImage::fromUploadedFile($createAddress->image),
            $this->addressRepository->getNextSequence(),
            $createAddress->mediaGroup,
            $createAddress->groups,
            $createAddress->lastName,
            $createAddress->firstName,
            $createAddress->email,
            $createAddress->street,
            $createAddress->number,
            $createAddress->postal,
            $createAddress->city,
            $createAddress->country,
            $createAddress->fax,
            $createAddress->telephone,
            $createAddress->website,
            $createAddress->note,
            $createAddress->btw,
            AddressLogo::fromUploadedFile($createAddress->logo),
            AddressBackground::fromUploadedFile($createAddress->background),
            $createAddress->sliderType
        );

        $translationDataTransfers = $createAddress->translations;


        $address = $createAddress->address;
        //-- Add languages
        array_map(
            function (AddressTranslationDataTransferObject $addressTranslationDataTransferObject) use ($address) {
                // this is added to the item automatically
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
            },
            $createAddress->translations
        );



        $this->addressRepository->add($address);
    }
}
