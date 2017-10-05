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
            'SELECT i.id, l.title, l.hidden, CONCAT(i.street, " ", i.number, ", ", i.city, " ", i.postal) as address, i.sequence
                FROM addresses as i 
                INNER JOIN addresses_lang as l ON l.addressId = i.id
                WHERE l.language = :language GROUP BY i.id', array('language' => $locale)
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