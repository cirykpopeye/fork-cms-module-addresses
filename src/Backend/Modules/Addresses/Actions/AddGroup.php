<?php

namespace Backend\Modules\Addresses\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model;
use Backend\Modules\Addresses\Domain\Group\Command\CreateGroup;
use Backend\Modules\Addresses\Domain\Group\Event\GroupCreated;
use Backend\Modules\Addresses\Domain\Group\GroupType;

/**
 * This is the add-action, it will display a form to create a new group
 */
class AddGroup extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute(): void
    {
        parent::execute();
        $form = $this->createForm(GroupType::class, new CreateGroup());

        $form->handleRequest($this->getRequest());

        if (!$form->isValid()) {
            $this->template->assign('form', $form->createView());
            $this->template->assign('mediaGroup', $form->getData()->images);

            $this->parse();
            $this->display();

            return;
        }

        /** @var CreateGroup $createGroup */
        $createGroup = $form->getData();

        // The command bus will handle the saving of the category in the database.
        $this->get('command_bus')->handle($createGroup);


        $this->get('event_dispatcher')->dispatch(
            GroupCreated::EVENT_NAME,
            new GroupCreated($createGroup->group)
        );
        //-- Return to index
        $this->redirect(
            Model::createURLForAction('Groups') . '?report=added&var=' . $createGroup->group->getId()
        );
    }
}
