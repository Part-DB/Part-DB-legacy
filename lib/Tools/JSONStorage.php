<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 01.01.2018
 * Time: 13:27
 */

namespace PartDB\Tools;

/**
 * This class provides the possibilty to save and retriev data to/from a special JSON file.
 * @package PartDB\Tools
 */
class JSONStorage
{
    /** @var string The filepath were the data should be saved. */
    protected $file_path;
    /** @var int The options that should be used when calling json_encode. */
    protected $json_encode_options;

    /** @var array The representation of the datastructure from the file. */
    protected $database;

    /**
     * Creates a new JSONStorage object.
     * @param $file_path string The path of the file, which should be used as database (as absolute unix path). File will be created, if it not exists yet.
     * @param int $json_encode_options The options that should be used when calling json_encode (e.g. JSON_PRETTY_PRINT)
     */
    public function __construct(string $file_path, int $json_encode_options = 0)
    {
        /*
        if (!) {
            throw new \InvalidArgumentException(_("$file_path ist kein gültiger Dateipfad!"));
        }*/

        if (!strcontains($file_path, ".json")) {
            throw new \InvalidArgumentException(_("$file_path muss eine .json Datei sein!"));
        }

        if (!isPathabsoluteAndUnix($file_path)) {
            throw new \InvalidArgumentException(_("$file_path muss ein absoluter Unixpfad sein!"));
        }

        $this->json_encode_options = $json_encode_options;
        $this->file_path = $file_path;

        //Read file or init database.
        $this->read();
    }

    /**
     * When called, the current database structure is written to file.
     * @throws \RuntimeException Thrown when internal database is invalid.
     */
    public function write()
    {
        //Do some simple checks to $database.
        if (!is_array($this->database)) {
            throw new \RuntimeException(_("Interner Datenbankfehler!"));
        }

        $json = json_encode($this->database, $this->json_encode_options);
        file_put_contents($this->file_path, $json);
    }

    /**
     * When called, the current database structure is read from file.
     * If file is not existing, then an empty database is created.
     */
    public function read()
    {
        if (is_file($this->file_path)) {
            //Read json file and update $this->database.
            $text = file_get_contents($this->file_path);
            $data = json_decode($text, true);

            //Do some simple checks to $data:
            if (!(is_array($data) || is_object($data))) {
                throw new \RuntimeException(_("Die JSON-Datei enthält fehlerhafte Daten!"));
            }

            //Apply to $this->database.
            $this->database = $data;
        } else {
            //Create an empty structure
            $this->database = array();
        }
    }

    /**
     * Returns the count of all items in the JSON storage.
     * @return int The amount of all items in the JSON storage.
     */
    public function countItems() : int
    {
        return count($this->database);
    }

    /**
     * Check if an item with the given key exists in the storage.
     * @param $key string The key for the item.
     * @return bool True if the item exists, false if not.
     */
    public function itemExists(string $key) : bool
    {
        return array_key_exists($key, $this->database);
    }

    /**
     * Returns the item for the given key.
     * If no item exists for the given key, by default a RuntimeException is thrown. Use $throw_exception to disable that.
     * @param $key string The key for which the item should be get.
     * @param bool $throw_exception Set this to false, if you dont want to get an exception thrown, if no item with the key exists. Instead null is returned.
     * @return mixed|null The item with the given key. Null if $throw_exception is false, and no item with this key was found.
     */
    public function getItem(string $key, bool $throw_exception = true)
    {
        if (!$this->itemExists($key)) {
            if ($throw_exception) {
                throw new \RuntimeException(sprintf(_("Kein Item mit Schlüssel %s vorhanden!"), $key));
            } else {
                return null;
            }
        }

        return $this->database[$key];
    }

    /**
     * Adds an new item to the storage with the given key string.
     * @param $key string The key under which the data should be saved.
     * @param $data mixed The data that should be written.
     * @param $write_data bool Set this to false, if the data should not be written instantly to file. Call write() manually later.
     */
    public function addItem(string $key, $data, bool $write_data = true)
    {
        if ($this->itemExists($key)) {
            throw new \RuntimeException(sprintf(_("Es existiert bereits ein Item mit dem Schlüssel %s!"), $key));
        }

        $this->database[$key] = $data;
        if ($write_data) {
            $this->write();
        }
    }

    /**
     * Adds an new item to the storage with the given key string.
     * @param $key string The key under which the data should be saved.
     * @param $write_data bool Set this to false, if the data should not be written instantly to file. Call write() manually later.
     * @param $data mixed The new data.
     * @param $create_when_not_exist bool Set this to true, if a new item should be created, when the no item with this key exists yet. Otherwise an exception is thrown.
     */
    public function editItem(string $key, bool $data, bool $write_data = true, bool $create_when_not_exist = false)
    {
        if (!$create_when_not_exist && !$this->itemExists($key)) {
            throw new \RuntimeException(sprintf(_("Es existiert bereits ein Item mit dem Schlüssel %s!"), $key));
        }

        $this->database[$key] = $data;
        if ($write_data) {
            $this->write();
        }
    }

    /**
     * Deletes an item from the storage.
     * @param $key string The key of the item which should be deleted
     * @param $write_data bool Set this to false, if the data should not be written instantly to file. Call write() manually later.
     */
    public function deleteItem(string $key, bool $write_data = true)
    {
        if (!$this->itemExists($key)) {
            throw new \RuntimeException(sprintf(_("Kein Item mit Schlüssel %s vorhanden!"), $key));
        }

        unset($this->database[$key]);

        if ($write_data) {
            $this->write();
        }
    }

    /**
     * Change the key of an item.
     * @param $old_key string The key of the item which should be renamed.
     * @param $new_key string The new key name.
     * @param $write_data bool Set this to false, if the data should not be written instantly to file. Call write() manually later.
     */
    public function renameItem(string $old_key, string $new_key, bool $write_data = true)
    {
        if (!$this->itemExists($old_key)) {
            throw new \RuntimeException(sprintf(_("Kein Item mit Schlüssel %s vorhanden!"), $old_key));
        }
        $data = $this->getItem($old_key);
        //Create a new item with new name
        $this->addItem($new_key, $data, false);
        //Remove old item.
        $this->deleteItem($old_key, $write_data);
    }

    /**
     * Returns an array/list with all keys, that contains the $filter string.
     * @param string $filter Every key which gets returned has to contain this string. Set to "" to get all keys unfiltered.
     * @return array An array with all keys.
     */
    public function getKeyList(string $filter = "") : array
    {
        $keys = array_keys($this->database);
        if ($filter == "") {
            return $keys;
        } else {
            return array_filter($keys, function ($var) use ($filter) {
                return strcontains($var, $filter);
            });
        }
    }
}
