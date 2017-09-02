<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 24.08.2017
 * Time: 17:02
 */

namespace PartDB\Interfaces;

interface IAPIModel
{
    /**
     * Returns a Array representing the current object.
     * @param bool $verbose If true, all data about the current object will be printed, otherwise only important data is returned.
     * @return array A array representing the current object.
     */
    public function getAPIArray($verbose = false);
}
