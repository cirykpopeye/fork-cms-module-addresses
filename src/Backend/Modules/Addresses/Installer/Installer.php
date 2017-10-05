<?php

namespace Backend\Modules\Addresses\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\Addresses\Domain\Group\GroupTranslation;
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressTranslation;
use Common\ModuleExtraType;

/**
 * Installer for the categories module
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // add the schema of the entity to the database
        Model::get('fork.entity.create_schema')->forEntityClass(Group::class);
        // add the schema of the entity to the database
        Model::get('fork.entity.create_schema')->forEntityClass(Grouptranslation::class);
        // add the schema of the entity to the database
        Model::get('fork.entity.create_schema')->forEntityClass(Address::class);
        // add the schema of the entity to the database
        Model::get('fork.entity.create_schema')->forEntityClass(AddressTranslation::class);

        // add module to modules
        $this->addModule('Addresses');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'AddGroup');
        $this->setActionRights(1, $this->getModule(), 'DeleteGroup');
        $this->setActionRights(1, $this->getModule(), 'EditGroup');
        $this->setActionRights(1, $this->getModule(), 'Groups');
        $this->setActionRights(1, $this->getModule(), 'AddAddress');
        $this->setActionRights(1, $this->getModule(), 'EditAddress');
        $this->setActionRights(1, $this->getModule(), 'DeleteAddress');
        $this->setActionRights(1, $this->getModule(), 'Addresses');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationAddressId = $this->setNavigation(
            $navigationModulesId,
            'Addresses'
        );

        $this->setNavigation(
            $navigationAddressId,
            'Groups',
            'addresses/groups',
            ['addresses/add_group', 'addresses/edit_group']
        );

        $this->setNavigation(
            $navigationAddressId,
            'Addresses',
            'addresses/addresses',
            ['addresses/add_address', 'addresses/edit_address']
        );

        //-- Add extra
        Model::insertExtra(ModuleExtraType::widget(), 'Addresses', 'ShowAddresses');
        Model::insertExtra(ModuleExtraType::block(), 'Addresses');
    }
}
