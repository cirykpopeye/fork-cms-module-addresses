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
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressRepository;

/**
 * This action will delete a address
 */
class DeleteAddress extends BackendBaseActionDelete
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @var Address
     */
    private $address;

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
        if ($this->id !== null && $this->address instanceof Address) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();
            $this->addressRepository->remove($this->address);
            $this->redirect(BackendModel::createURLForAction('Addresses') . '&report=address-deleted');
        } else {
            // something went wrong
            $this->redirect(BackendModel::createURLForAction('Addresses') . '&error=non-existing');
        }
    }

    private function getData() {
        //-- Fetch repo
        $this->addressRepository = $this->get('addresses.repository.address');
        $this->address = $this->addressRepository->find($this->id);
    }
}
