<?php

namespace Backend\Modules\Addresses\Domain\Group\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressRepository;
use Backend\Modules\Addresses\Domain\Group\GroupTranslation;
use Backend\Modules\Addresses\Domain\Group\GroupTranslationDataTransferObject;
use Backend\Modules\Addresses\Exceptions\TranslationNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UpdateGroupHandler
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * UpdateGroupHandler constructor.
     */
    public function __construct()
    {
        $this->addressRepository = Model::get('addresses.repository.address');
    }

    /**
     * @param UpdateGroup $updateGroup
     * @throws \Backend\Core\Engine\Exception
     * @throws \Backend\Modules\Addresses\Exceptions\GroupNotFoundException
     * @throws \Exception
     */
    public function handle(UpdateGroup $updateGroup)
    {
        $group = $updateGroup->getGroup();

        /** @var Address $address */
        foreach($this->addressRepository->findAll() as $address) {
            //-- Check if exists in group already
            if($group->getAddresses()->contains($address)) {

                //-- Check if selected again
                if(!$updateGroup->getAddresses()->contains($address)) {
                    //-- Not selected anymore, remove
                    $address->removeGroup($group);
                }
            } else {

                //-- Is it check now?
                if($updateGroup->getAddresses()->contains($address)) {
                    //-- Selected now, add
                    $address->addGroup($group);
                }
            }

            //-- Update the address
            $this->addressRepository->getOwnEntityManager()->persist($address);
        }

        $group->update();

        array_map(
            function (GroupTranslationDataTransferObject $groupTranslationDataTransferObject) use ($group) {
                try {
                    $translation = $group->getTranslation($groupTranslationDataTransferObject->getLocale());

                    $translation->update(
                        $groupTranslationDataTransferObject->title,
                        $groupTranslationDataTransferObject->description,
                        $groupTranslationDataTransferObject->summary,
                        $groupTranslationDataTransferObject->hidden
                    );
                } catch (TranslationNotFoundException $translationNotFound) {
                    // this is added to the category automatically
                    new GroupTranslation(
                        $groupTranslationDataTransferObject->getLocale(),
                        $group,
                        $groupTranslationDataTransferObject->title,
                        $groupTranslationDataTransferObject->description,
                        $groupTranslationDataTransferObject->summary,
                        $groupTranslationDataTransferObject->hidden,
                        $groupTranslationDataTransferObject->meta
                    );
                }
            },
            $updateGroup->translations
        );
    }
}
