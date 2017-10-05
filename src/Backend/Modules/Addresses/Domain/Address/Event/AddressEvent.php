<?php

namespace Backend\Modules\Addresses\Domain\Address\Event;

use Backend\Modules\Addresses\Domain\Address\Address;
use Symfony\Component\EventDispatcher\Event;

abstract class AddressEvent extends Event
{
    /** @var Address */
    private $address;

    /**
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}