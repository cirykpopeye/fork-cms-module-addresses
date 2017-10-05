<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Address\Command\UpdateAddress;
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressType;


final class EditAddress extends ActionEdit
{
    /**
     * @throws \Backend\Modules\Addresses\Exceptions\AddressNotFoundException
     * @throws \Common\Exception\RedirectException
     * @throws \Exception
     */
    public function execute(): void
    {
        parent::execute();

        $address = $this->getAddress();
        $form = $this->createForm(AddressType::class, new UpdateAddress($address));

        $form->handleRequest($this->getRequest());

        if (!$form->isValid()) {
            $this->template->assign('form', $form->createView());
            $this->template->assign('id', $address->getId());
            $this->template->assign('name', $address->getTranslation(Locale::workingLocale())->getTitle());
            $this->template->assign('currentImage', $address->getImage()->getWebPath('source'));
            $this->template->assign('mediaGroup', $form->getData()->mediaGroup);

            $this->parse();
            $this->display();

            return;
        }

        /** @var UpdateAddress $updateAddress */
        $updateAddress = $form->getData();

        $this->get('command_bus')->handle($updateAddress);

        $this->redirect(
            Model::createURLForAction('Addresses', null, null, array('report' => 'updated', 'var' => $updateAddress->getAddress()->getId()))
        );
    }

    /**
     * @throws \Common\Exception\RedirectException
     * @throws \Exception
     */
    private function returnNonExisting() {
        $this->redirect(Model::createURLForAction('Addresses', null, null, ['error' => 'non-existing']));
    }

    /**
     * @return Address
     * @throws \Common\Exception\RedirectException
     * @throws \Exception
     */
    private function getAddress(): Address
    {
        $addressId = $this->getRequest()->query->getInt('id');
        if ($addressId === null) {
            $this->returnNonExisting();
        }

        $address = $this->get('addresses.repository.address')->find($addressId);

        if (!$address instanceof Address) {
            $this->returnNonExisting();
        }

        return $address;
    }
}
