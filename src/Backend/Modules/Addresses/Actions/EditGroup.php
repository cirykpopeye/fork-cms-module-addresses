<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Group\Command\UpdateGroup;
use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\Addresses\Domain\Group\GroupType;

final class EditGroup extends ActionEdit
{
    /**
     * @throws \Backend\Modules\Addresses\Exceptions\GroupNotFoundException
     * @throws \Common\Exception\RedirectException
     * @throws \Exception
     */
    public function execute(): void
    {
        parent::execute();

        $group = $this->getGroup();

        $form = $this->createForm(GroupType::class, new UpdateGroup($group));

        $form->handleRequest($this->getRequest());

        if (!$form->isValid()) {
            $this->template->assign('form', $form->createView());
            $this->template->assign('id', $group->getId());
            $this->template->assign('name', $group->getTranslation(Locale::workingLocale())->getTitle());
            $this->template->assign('currentImage', $group->getImage()->getWebPath('source'));

            $this->parse();
            $this->display();

            return;
        }

        /** @var UpdateGroup $updateGroup */
        $updateGroup = $form->getData();

        $this->get('command_bus')->handle($updateGroup);

        $this->redirect(
            Model::createURLForAction('EditGroup', null, null, array('report' => 'updated', 'var' => $updateGroup->getGroup()->getId(), 'id' => $updateGroup->getGroup()->getId()))
        );
    }

    /**
     * @throws \Common\Exception\RedirectException
     * @throws \Exception
     */
    private function returnNonExisting() {
        $this->redirect(Model::createURLForAction('Groups', null, null, ['error' => 'non-existing']));
    }

    /**
     * @return Group
     * @throws \Common\Exception\RedirectException
     * @throws \Exception
     */
    private function getGroup(): Group
    {
        $groupId = $this->getRequest()->query->getInt('id');
        if ($groupId === null) {
            $this->returnNonExisting();
        }

        $group = $this->get('addresses.repository.group')->find($groupId);

        if (!$group instanceof Group) {
            $this->returnNonExisting();
        }
        return $group;
    }
}
