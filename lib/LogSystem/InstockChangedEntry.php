<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 01.10.2018
 * Time: 13:48
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
    protected $old_instock, $new_instock;

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
        } catch (Exception $ex) {

        }

        //Fill our extra values.

        $extra_string = parent::getExtra();
        $extra_array = static::deserializeExtra($extra_string);

        $this->old_instock = $extra_array['o'];
        $this->new_instock = $extra_array['n'];
        $this->comment = $extra_array['c'];
        $this->price = $extra_array['p'];
    }

    /**
     * Returns the instock value, the part had before the change
     * @return int The old instock value.
     */
    public function getOldInstock()
    {
        return $this->old_instock;
    }

    /**
     * Returns the instock value, the part had after the change.
     * @return int The new instock value.
     */
    public function getNewInstock()
    {
        return $this->new_instock;
    }

    /**
     * Returns the comment associated with the change.
     * @return string The comment.
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function getExtra()
    {
        $difference = $this->getDifference();
        if($difference > 0 ) {
            $difference = "+".$difference;
        }

        return $this->getTypeString(). "; Alter Wert: " . $this->getOldInstock() .
            "; Neuer Wert: ". $this->getNewInstock() . " (" . $difference . ")" .
            "; Preis: " . $this->getPriceMoneyString(true) .
            "; Kommentar: " . $this->getComment();
    }

    /**
     * Returns the price that has to be payed for the change.
     * @param $absolute bool Set this to true, if you want only get the absolute value of the price (without minus)
     * @return float
     */
    public function getPrice($absolute = false)
    {
        if($absolute) {
            return abs($this->price);
        }
        return $this->price;
    }

    /**
     * Returns the price as money string.
     * @param bool $absolute
     * @return string
     */
    public function getPriceMoneyString($absolute = true)
    {
        $float = $this->getPrice($absolute);
        return floatToMoneyString($float);
    }

    /**
     * Returns the difference value of the change ($new_instock - $old_instock).
     * @param bool $absolute Set this to true if you want only the absolute value of the difference.
     * @return float|int Difference is positive if instock has increased, negative if decreased.
     */
    public function getDifference($absolute = false)
    {
        $difference = $this->new_instock - $this->old_instock;
        if($absolute) {
            return abs($difference);
        } else {
            return $difference;
        }
    }

    /**
     * Checks if the Change was an withdrawal of parts.
     * @return bool True if the change was an withdrawal, false if not.
     */
    public function isWithdrawal()
    {
        return $this->new_instock < $this->old_instock;
    }

    /**
     * Returns an string description, if the Change was an withdrawal or an addition.
     * @return string
     */
    public function getTypeString()
    {
        if($this->isWithdrawal()) {
            return _("Entnahme");
        } else {
            return _("Zugabe");
        }
    }

    /**
     * Returns the a text representation of the target
     * @return string The text describing the target
     */
    public function getTargetText()
    {
        $part_name = ($this->element != null) ? $this->element->getName() : $this->getTargetID();
        return Log::targetTypeIDToString($this->getTargetType()) . ": " . $part_name;
    }

    /**
     * Return a link to the target. Returns empty string if no link is available.
     * @return string the link to the target.
     */
    public function getTargetLink()
    {
        return Log::generateLinkForTarget($this->getTargetType(), $this->getTargetID());
    }

    /**
     * This function converts the given $extra array to a form, that can be written into the extra field.
     * @param $extra
     * @return false|string
     */
    protected static function serializeExtra($extra)
    {
        return json_encode($extra);
    }

    /**
     * This function converts the string from the extra field, to an array/object.
     * @param $string
     * @return mixed
     */
    protected static function deserializeExtra($string)
    {
        return json_decode($string, true);
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
    public static function add(&$database, &$current_user, &$log, &$part, $old_instock, $new_instock, $comment = null)
    {
        //Do some checks
        if (!$part instanceof Part) {
            throw new \RuntimeException(_('$element muss vom Typ Part sein!'));
        }

        if (!is_int($old_instock) || !is_int($new_instock)) {
            throw new \RuntimeException(_('$old_instock und $new_instock müssen vom Typ int sein'));
        }

        if ($new_instock < 0 || $old_instock < 0) {
            throw new \RuntimeException(_('Instock Werte müssen positiv sein!'));
        }

        if ($new_instock == $old_instock) {
            throw new \RuntimeException(_('Die Anzahl der vorhanden Teile muss sich ändern um ein InstockChangedEntry erzeugen zu können!'));
        }

        if($comment === null) {
            $comment = $current_user->getDefaultInstockChangeComment();
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
            static::serializeExtra($extra_array)
        );
    }

}