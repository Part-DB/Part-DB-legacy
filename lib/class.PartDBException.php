<?php

/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 04.01.2017
 * Time: 17:15
 */
abstract class PartDBException extends Exception
{
    public function __construct($message = "", Exception $cause, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}