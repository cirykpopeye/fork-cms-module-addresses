<?php

namespace Backend\Modules\Addresses\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Locale;
use Backend\Modules\Addresses\Domain\Group\GroupDataGrid;

/**
 * This is the index-action (default), it will display the overview
 */
class Groups extends BackendBaseActionIndex
{
    /**
     * Execute the action
     */
    public function execute(): void
    {
        parent::execute();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Load the datagrid
     */
    private function loadDataGrid()
    {
        $this->dataGrid = new GroupDataGrid(Locale::workingLocale());
    }

    /**
     * Parse the datagrid and the reports
     */
    protected function parse(): void
    {
        parent::parse();
        $this->template->assign('dataGrid', (string) $this->dataGrid->getContent());
    }
}
