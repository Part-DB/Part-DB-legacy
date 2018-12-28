<?php

namespace PartDB\Interfaces;

/**
 * Elements implementing that interface contain infos about when they were created, and last modified.
 * Interface IHasModifiedCreatedInfos
 * @package PartDB\Interfaces
 */
interface IHasModifiedCreatedInfos
{
    /**
     * Returns the last time when the element was modified.
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *          When false, the raw value from the DB is returned.
     * @return string The time of the last edit.
     */
    public function getLastModified(bool $formatted = true) : string;

    /**
     * Returns the date/time when the element was created.
     * @param $formatted bool When true, the date gets formatted with the locale and timezone settings.
     *       When false, the raw value from the DB is returned.
     * @return string The creation time of the part.
     */
    public function getDatetimeAdded(bool $formatted = true) : string;
}
