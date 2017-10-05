<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 4/07/17
 * Time: 16:19
 */

namespace Backend\Modules\Addresses\Domain\Address\Command;

use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressRepository;

final class SortAddressesHandler
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * SortAddressesHandler constructor.
     * @param AddressRepository $addressRepository
     */
    function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function handle(SortAddresses $sortProducts) {
        foreach ($sortProducts->getIds() as $sequence => $id) {
            /** @var Address $address */
            $address = $this->addressRepository->find($id);

            if ($address === null) {
                continue;
            }

            $address->setSequence($sequence);
        }
    }
}