<?php

namespace Frontend\Modules\Addresses\Widgets;

use Backend\Modules\Addresses\Entity\Group;
use Backend\Modules\Addresses\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Language\Locale;

/**
 * This is the detail widget.
 */
class ShowGroup extends FrontendBaseWidget
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var ArrayCollection|Group
     */
    private $group;

    /**
     * Execute the extra
     */
    public function execute(): void
    {
        parent::execute();
        //-- Get the data
        $this->getData();
        //-- Parse
        $this->parse();
        //-- Load template
        $this->loadTemplate();
    }

    /**
     * Fetch data
     */
    public function getData() {
        $this->groupRepository = $this->get('addresses.repository.group');
        $this->group = $this->groupRepository->find($this->data['id']);
    }

    /**
     * Parse to template
     */
    protected function parse() {
        $this->tpl->assign('group', $this->group);
        //-- Assign a frontend language, so translations can be retrieved.
        $this->tpl->assign('language', Locale::frontendLanguage());

    }
}