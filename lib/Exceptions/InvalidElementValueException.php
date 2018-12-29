<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 27.12.2018
 * Time: 00:18
 */

namespace PartDB\Exceptions;

/**
 * This exception is thrown, if the checkValueValidity notices, that a value is invalid and this can not be corrected!
 * @package PartDB\Exceptions
 */
class InvalidElementValueException extends \InvalidArgumentException
{
}
