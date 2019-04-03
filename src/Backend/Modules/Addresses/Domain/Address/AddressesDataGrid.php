<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Backend\Core\Language\Locale;


class AddressesDataGrid extends DataGridDatabase
{
    /**
     * AddressesDataGrid constructor.
     * @param Locale $locale
     * @throws \Exception
     * @throws \SpoonDatagridException
     */
    public function __construct(Locale $locale)
    {
        parent::__construct(
            'SELECT i.id, l.title, l.hidden, CONCAT(i.street, " ", i.number, ", ", i.city, " ", i.postal) as address, GROUP_CONCAT(gl.title) as `groups`, i.sequence
                FROM addresses as i 
                INNER JOIN addresses_lang as l ON l.addressId = i.id
                INNER JOIN addresses_addresses_groups as ag ON i.id = ag.address_id
                INNER JOIN addresses_groups_lang as gl ON gl.groupId = ag.group_id
                WHERE l.language = :language AND gl.language = :language GROUP BY i.id', array('language' => $locale)
        );

        $this->enableSequenceByDragAndDrop();
        $this->setSortingColumns(array('sequence'), 'sequence');
        $this->setAttributes(['data-action' => 'SortAddresses']);

        if (Authentication::isAllowedAction('EditAddress')) {
            $editUrl = Model::createURLForAction('EditAddress', null, null, ['id' => '[id]'], false);
            $this->setColumnURL('title', $editUrl);
            $this->addColumn('edit', null, Language::lbl('Edit'), $editUrl, Language::lbl('Edit'));
        }
    }

    /**
     * @param Locale $locale
     *
     * @return string
     */
    public static function getHtml(Locale $locale)
    {
        $dataGrid = new self($locale);
        return (string) $dataGrid->getContent();
    }
}