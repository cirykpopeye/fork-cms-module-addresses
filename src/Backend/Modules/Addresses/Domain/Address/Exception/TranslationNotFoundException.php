<?php
namespace Backend\Modules\Addresses\Exceptions;
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 3/02/17
 * Time: 15:29
 */
class TranslationNotFoundException extends \Exception
{

    /**
     * ProductNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('Translation not found');
    }
}