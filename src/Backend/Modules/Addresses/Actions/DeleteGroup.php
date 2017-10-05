<?php

namespace Backend\Modules\Addresses\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\Addresses\Domain\Group\GroupRepository;

/**
 * This action will delete a group
 */
class DeleteGroup extends BackendBaseActionDelete
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var Group
     */
    private $group;

    /**
     * Execute the action
     */
    public function execute(): void
    {
        // get parameters
        $this->id = $this->getRequest()->query->getInt('id');
        //-- Fetch the data
        $this->getData();
        // does the item exist
        if ($this->id !== null && $this->group instanceof Group) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            $this->groupRepository->remove($this->group);

            $this->redirect(BackendModel::createURLForAction('Groups') . '&report=deleted');
        } else {
            // something went wrong
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=non-existing');
        }
    }

    private function getData() {
        //-- Fetch repo
        $this->groupRepository = $this->get('addresses.repository.group');
        $this->group = $this->groupRepository->find($this->id);
    }
}
