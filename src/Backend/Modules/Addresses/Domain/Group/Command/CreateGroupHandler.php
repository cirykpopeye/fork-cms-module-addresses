<?php

namespace Backend\Modules\Addresses\Domain\Group\Command;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\Addresses\Domain\Group\GroupTranslationDataTransferObject;
use Backend\Modules\Addresses\Domain\Group\GroupTranslation;
use Backend\Modules\Addresses\Domain\Group\GroupRepository;
use Backend\Modules\Addresses\Domain\Group\GroupImage;
use Common\ModuleExtraType;

final class CreateGroupHandler
{
    /** @var GroupRepository */
    private $groupRepository;

    /**
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param CreateGroup $createGroup
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(CreateGroup $createGroup)
    {
        $createGroup->group = new Group(
            Authentication::getUser()->getUserId(),
            GroupImage::fromUploadedFile($createGroup->image),
            $this->groupRepository->getNextSequence(),
            $this->getNewExtraId()
        );
        $group = $createGroup->group;

        foreach($createGroup->addresses as $address) {
            $address->addGroup($group);
        }

        array_map(
            function (GroupTranslationDataTransferObject $groupTranslationDataTransferObject) use ($group) {
                // this is added to the category automatically
                // $locale, $category, $title, $description, $summary, $hidden
                new GroupTranslation(
                    $groupTranslationDataTransferObject->getLocale(),
                    $group,
                    $groupTranslationDataTransferObject->title,
                    $groupTranslationDataTransferObject->description,
                    $groupTranslationDataTransferObject->summary,
                    $groupTranslationDataTransferObject->hidden,
                    $groupTranslationDataTransferObject->meta
                );
            },
            $createGroup->translations
        );

        $this->groupRepository->add($group);
    }

    /**
     * @return int
     * @throws \Backend\Core\Engine\Exception
     */
    private function getNewExtraId()
    {
        return BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'Addresses',
            'ShowGroup'
        );
    }
}
