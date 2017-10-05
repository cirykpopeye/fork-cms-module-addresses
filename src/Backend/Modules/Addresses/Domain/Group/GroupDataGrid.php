<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Backend\Core\Language\Locale;

class GroupDataGrid extends DataGridDatabase
{
    /**
     * GroupDataGrid constructor.
     * @param Locale $locale
     * @throws \Exception
     */
    public function __construct(Locale $locale)
    {
        parent::__construct(
            'SELECT i.id, l.title, l.hidden, i.sequence
                FROM addresses_groups as i 
                INNER JOIN addresses_groups_lang as l ON l.groupId = i.id
                WHERE l.language = :language', array('language' => $locale)
        );

        $this->enableSequenceByDragAndDrop();
        $this->setSortingColumns(array('sequence'), 'sequence');
        $this->setAttributes(['data-action' => 'SortGroups']);

        if (Authentication::isAllowedAction('EditGroup')) {
            $editUrl = Model::createURLForAction('EditGroup', null, null, ['id' => '[id]'], false);
            $this->setColumnURL('title', $editUrl);
            $this->addColumn('edit', null, Language::lbl('Edit'), $editUrl, Language::lbl('Edit'));
        }
    }

    /**
     * @param Locale $locale
     * @return string
     */
    public static function getHtml(Locale $locale)
    {
        $dataGrid = new self($locale);
        return (string) $dataGrid->getContent();
    }
}