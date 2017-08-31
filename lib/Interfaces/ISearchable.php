<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 29.08.2017
 * Time: 14:25
 */

namespace PartDB\Interfaces;

use Exception;
use PartDB\Database;
use PartDB\Log;
use PartDB\User;

interface ISearchable
{
    /**
     * Search elements by name.
     *
     * @param Database  &$database              reference to the database object
     * @param User      &$current_user          reference to the user which is logged in
     * @param Log       &$log                   reference to the Log-object
     * @param string    $keyword                the search string
     * @param boolean   $exact_match            @li If true, only records which matches exactly will be returned
     *                                          @li If false, all similar records will be returned
     *
     * @return array    all found elements as a one-dimensional array of objects,
     *                  sorted by their names
     *
     * @throws Exception if there was an error
     */
    public static function search(&$database, &$current_user, &$log, $keyword, $exact_match);
}
