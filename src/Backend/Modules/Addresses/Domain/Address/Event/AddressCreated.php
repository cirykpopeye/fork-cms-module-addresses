<?php

namespace Backend\Modules\Addresses\Domain\Address\Event;

final class AddressCreated extends AddressEvent
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'addresses.event.address_created';
}
