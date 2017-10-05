<?php

namespace Backend\Modules\Addresses\Domain\Group\Event;

final class GroupCreated extends GroupEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'addresses.event.group_created';
}
