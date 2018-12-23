<?php

/**
 *
 * Part-DB Version 0.4+ "nextgen"
 * Copyright (C) 2016 - 2018 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 */

namespace PartDB\LogSystem;

use PartDB\Base\NamedDBElement;
use PartDB\Database;
use PartDB\Log;
use PartDB\Part;
use PartDB\User;

class InstockChangedEntry extends BaseEntry
{

    /**
     * @var Part
     */
    protected $element;

    /** @var int */
    protected $old_instock;
    protected $new_instock;

    /**
     * @var string
     */
    protected $comment;
    /** @var float */
    protected $price;

    public function __construct(Database $database, User $current_user, Log $log, $id, $db_data = null)
    {
        parent::__construct($database, $current_user, $log, $id, $db_data);

        //Check if we have selcted the right type
        if ($this->getTypeID() != Log::TYPE_INSTOCKCHANGE) {
            throw new \RuntimeException(_("Falscher Logtyp!"));
        }

        try {
            $class = Log::targetTypeIDToClass($this->getTargetType());
            $this->element = new $class($database, $current_user, $log, $this->getTargetID());
        } catch (\Exception $ex) {
        }

        //Fill our extra values.

        $extra_array = $this->deserializeExtra();

        $this->old_instock = $extra_array['o'];
        $this->new_instock = $extra_array['n'];
        $this->comment = $extra_array['c'];
        $this->price = $extra_array['p'];
    }

    /**
     * Returns the instock value, the part had before the change
     * @return int The old instock value.
     */
    public function getOldInstock() : int
    {
        return $this->old_instock;
    }

    /**
     * Returns the old instock value as string. If the instockvalue is -2, than "[Unknown]" will be returned.
     * @return string The instock as string.
     */
    public function getOldInstockString()
    {
        if ($this->getOldInstock() == Part::INSTOCK_UNKNOWN)
        {
            return _("[Unbekannt]");
        } else {
            return (string) $this->getOldInstock();
        }
    }

    /**
     * Returns the instock value, the part had after the change.
     * @return int The new instock value.
     */
    public function getNewInstock() : int
    {
        return $this->new_instock;
    }

    /**
     * Returns the new instock value as string. If the instockvalue is -2, than "[Unknown]" will be returned.
     * @return string The instock as string.
     */
    public function getNewInstockString()
    {
        if ($this->getNewInstock() == Part::INSTOCK_UNKNOWN)
        {
            return _("[Unbekannt]");
        } else {
            return (string) $this->getNewInstock();
        }
    }

    /**
     * Returns the comment associated with the change.
     * @return string The comment.
     */
    public function getComment() : string
    {
        return $this->comment;
    }

    public function getExtra(bool $html = false) : string
    {
        $difference = $this->getDifference();
        if ($difference > 0) {
            $difference = "+".$difference;
        }

        //Dont show the difference string, if one of the stock is unknown.
        $difference_str = "";
        if ($difference != 0) {
            $difference_str = " (" . $difference . ")";
        }

        return $this->getTypeString(). "; Alter Wert: " . $this->getOldInstockString() .
            "; Neuer Wert: ". $this->getNewInstockString() . $difference_str .
            "; Preis: " . $this->getPriceMoneyString(true) .
            "; Kommentar: " . $this->getComment();
    }

    /**
     * Returns the price that has to be payed for the change.
     * @param $absolute bool Set this to true, if you want only get the absolute value of the price (without minus)
     * @return float
     */
    public function getPrice(bool $absolute = false) : float
    {
        if ($absolute) {
            return abs($this->price);
        }
        return $this->price;
    }

    /**
     * Returns the price as money string.
     * @param bool $absolute
     * @return string
     */
    public function getPriceMoneyString(bool $absolute = true) : string
    {
        $float = $this->getPrice($absolute);
        return floatToMoneyString($float);
    }

    /**
     * Returns the difference value of the change ($new_instock - $old_instock).
     * @param bool $absolute Set this to true if you want only the absolute value of the difference.
     * @return int Difference is positive if instock has increased, negative if decreased.
     */
    public function getDifference(bool $absolute = false) : int
    {
        if ($this->new_instock == Part::INSTOCK_UNKNOWN || $this->old_instock == Part::INSTOCK_UNKNOWN) {
            return 0;
        }

        $difference = $this->new_instock - $this->old_instock;
        if ($absolute) {
            return abs($difference);
        } else {
            return $difference;
        }
    }

    /**
     * Checks if the Change was an withdrawal of parts.
     * @return bool True if the change was an withdrawal, false if not.
     */
    public function isWithdrawal() : bool
    {
        return $this->new_instock < $this->old_instock;
    }

    /**
     * Returns an string description, if the Change was an withdrawal or an addition.
     * @return string
     */
    public function getTypeString() : string
    {
        if ($this->isWithdrawal()) {
            return _("Entnahme");
        } else {
            return _("Zugabe");
        }
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    public function getTargetText() : string
    {
        $part_name = ($this->element != null) ? $this->element->getName() : $this->getTargetID();
        return Log::targetTypeIDToString($this->getTargetType()) . ": " . $part_name;
    }

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    public function getTargetLink() : string
    {
        return Log::generateLinkForTarget($this->getTargetType(), $this->getTargetID());
    }


    /**
     * Adds a new log entry to the database.
     * @param $database Database The database which should be used for requests.
     * @param $current_user User The database which should be used for requests.
     * @param $log Log The database which should be used for requests.
     * @param $part NamedDBElement The ip adress the user loggs in from
     *
     * @return static|BaseEntry The new created Entry.
     *
     * @throws \Exception
     */
    public static function add(Database &$database, User &$current_user, Log &$log, Part &$part, $old_instock, $new_instock, $comment = null)
    {
        if (!is_int($old_instock) || !is_int($new_instock)) {
            if (is_float($old_instock) || is_float($new_instock)) {
                throw new \RuntimeException(sprintf(_('Es können maximal %d Bauteile vorhanden sein!'), PHP_INT_MAX));
            }
            throw new \RuntimeException(_('$old_instock und $new_instock müssen vom Typ int sein'));
        }

        // Make an exception for unknown instock values.
        if (($new_instock != Part::INSTOCK_UNKNOWN && $old_instock != Part::INSTOCK_UNKNOWN) && ($new_instock < 0 || $old_instock < 0)) {
            throw new \RuntimeException(_('Instock Werte müssen positiv sein!'));
        }

        if ($new_instock == $old_instock) {
            return null; //We dont need to create an entry here.
            //throw new \RuntimeException(_('Die Anzahl der vorhanden Teile muss sich ändern um ein InstockChangedEntry erzeugen zu können!'));
        }

        if ($comment === null) {
            $comment = $current_user->getDefaultInstockChangeComment($new_instock < $old_instock);
        }

        $extra_array = array();
        $extra_array['o'] = $old_instock; //Old instock
        $extra_array['n'] = $new_instock; //New instock
        $extra_array['c'] = $comment;     //Comment
        $extra_array['p'] = $part->calculateInstockChangePrice($old_instock, $new_instock);

        $type_id = Log::TARGET_TYPE_PART;

        $level = Log::LEVEL_INFO;

        return static::addEntry(
            $database,
            $current_user,
            $log,
            Log::TYPE_INSTOCKCHANGE,
            $level,
            $current_user->getID(),
            $type_id,
            $part->getID(),
            $extra_array
        );
    }
}
