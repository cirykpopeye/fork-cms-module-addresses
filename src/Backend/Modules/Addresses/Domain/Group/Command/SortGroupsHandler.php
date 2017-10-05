<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 4/07/17
 * Time: 16:19
 */

namespace Backend\Modules\Addresses\Domain\Group\Command;

use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\Addresses\Domain\Group\GroupRepository;

final class SortGroupsHandler
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * SortGroupsHandler constructor.
     * @param GroupRepository $groupRepository
     */
    function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(SortGroups $sortGroups) {
        foreach ($sortGroups->getIds() as $sequence => $id) {
            /** @var Group $group */
            $group = $this->groupRepository->find($id);

            if ($group === null) {
                continue;
            }
            $group->setSequence($sequence);
        }
    }
}