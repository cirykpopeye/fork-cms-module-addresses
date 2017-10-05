<?php

namespace Backend\Modules\Addresses\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Address\Command\CreateAddress;
use Backend\Modules\Addresses\Domain\Address\Event\AddressCreated;
use Backend\Modules\Addresses\Domain\Address\AddressType;


/**
 * This is the add-action, it will display a form to create a new item
 */
class AddAddress extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute(): void
    {
        parent::execute();
        $createAddress = new CreateAddress();
        $form = $this->createAddressForm($createAddress);



        if (!$form->isValid()) {
            $this->template->assign('form', $form->createView());
            $this->template->assign('mediaGroup', $form->getData()->mediaGroup);

            $this->parse();
            $this->display();
            return;
        }

        // The command bus will handle the saving of the category in the database.
        $this->get('command_bus')->handle($form->getData());

        $this->get('event_dispatcher')->dispatch(
            AddressCreated::EVENT_NAME,
            new AddressCreated($createAddress->address)
        );

        $this->redirect(
            BackendModel::createURLForAction('Addresses', null, null, array('report' => 'added', 'var' => $createAddress->translations[(string) Locale::workingLocale()]->title))
        );
    }

    private function createAddressForm(CreateAddress $createAddress) {
        $form = $this->createForm(AddressType::class, $createAddress);
        $form->handleRequest($this->getRequest());
        return $form;
    }
}
