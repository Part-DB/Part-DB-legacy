<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 31.12.2018
 * Time: 18:12
 */

namespace PartDB\FilterSystem;


abstract class AbstractFilter
{
    const DEFAULT_ENTRY_LIMIT = 50;
    const DEFAULT_PAGE = 1;

    protected $entry_limit = self::DEFAULT_ENTRY_LIMIT;
    protected $page = self::DEFAULT_PAGE;


    abstract public function toQuery();


    /********************************************************************************
     * Getters
     ********************************************************************************/
    public function getEntryLimit() : int
    {
        return $this->entry_limit;
    }

    public function getPage() : int
    {
        return $this->page;
    }


    /*********************************************************************************
     * Setters
     *********************************************************************************/

    /**
     * Sets the maximum count of parts which are
     * @param int $limit
     */
    public function setEntryLimit(int $limit = self::DEFAULT_ENTRY_LIMIT)
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException(_('$limit darf nicht negativ sein!'));
        }
        $this->entry_limit = $limit;
    }

    public function setPage(int $page = self::DEFAULT_PAGE)
    {
        if ($page < 0) {
            throw new \InvalidArgumentException(_('$page darf nicht negativ sein!'));
        }

        $this->page = $page;
    }
}