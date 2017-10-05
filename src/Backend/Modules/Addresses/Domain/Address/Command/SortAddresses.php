<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 4/07/17
 * Time: 16:17
 */

namespace Backend\Modules\Addresses\Domain\Address\Command;


final class SortAddresses
{
    /**
     * @var string[]
     */
    private $ids;

    /**
     * SortAddresses constructor.
     * @param array $ids
     */
    function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return \string[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }
}