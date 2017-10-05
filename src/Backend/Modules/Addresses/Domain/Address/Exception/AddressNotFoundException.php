<?php
namespace Backend\Modules\Addresses\Exceptions;
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 3/02/17
 * Time: 15:29
 */
class AddressNotFoundException extends \Exception
{

    /**
     * ProductNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('Address not found');
    }
}