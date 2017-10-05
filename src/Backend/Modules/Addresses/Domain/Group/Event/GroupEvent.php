<?php

namespace Backend\Modules\Addresses\Domain\Group\Event;

use Backend\Modules\Addresses\Domain\Group\Group;
use Symfony\Component\EventDispatcher\Event;

abstract class GroupEvent extends Event
{
    /** @var Group */
    private $group;

    /**
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
