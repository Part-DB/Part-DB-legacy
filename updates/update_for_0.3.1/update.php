<?php

    include_once("class/database.php");
    include_once("class/system.php");
    include_once("user.php");

    /*
     * This is a custom update routine
     *
     * Return:
     *      true:   if success
     *      false:  if there was an error (that means the system has to restore itself)
     */
    function custom_routine(&$database, &$system, &$user, &$log)
    {
        // ...
        return true;
    }

    /*
     * This is a custom update routine
     *
     * Return:
     *      true:   if success
     *      false:  if there was an error (that means the system has to restore itself)
     */
    /*function custom_update(&$database, &$system, &$log)
    {
        $sql = array();

        // Datenbank an neue Struktur anpassen
        $sql[] = "CREATE TABLE internal (keyName CHAR(30) CHARACTER SET ASCII UNIQUE NOT NULL, keyValue CHAR(30));";
        $sql[] = "INSERT INTO internal SET keyName='dbVersion', keyValue='0';";
        $sql[] = "ALTER TABLE  `storeloc` ADD  `parentnode` int(11) NOT NULL default '0' AFTER  `name` ;";
        $sql[] = "ALTER TABLE  `storeloc` ADD  `is_full` boolean NOT NULL default false AFTER `parentnode` ;";

        return $system->execute_sql($sql, $log);
    }*/

    /*
     * This is a custom backup routine
     *
     * Return:
     *      true:   if success
     *      false:  if there was an error (that means the system has to restore itself)
     */
    /*function custom_backup()
    {
        return array();
    }*/

    /*
     * This is a custom restore routine
     */
    /*function custom_restore()
    {

    }*/

