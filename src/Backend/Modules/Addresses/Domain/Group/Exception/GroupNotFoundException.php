<?php
namespace Backend\Modules\Addresses\Domain\Group\Exception;
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 3/02/17
 * Time: 15:29
 */
class GroupNotFoundException extends \Exception
{

    /**
     * ProductNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('Group not found');
    }
}