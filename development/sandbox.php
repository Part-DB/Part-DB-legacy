<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

/*
 * This is the sandbox for developers. Here you can test functions and so on.
 * There is no *.tmpl file for the sandbox, you should print the HTML output here in this file.
 *
 * Have fun! :-)
 */

include_once('../start_session.php');


use PartDB\Database;
use PartDB\HTML;
use PartDB\Log;
use PartDB\System;
use PartDB\User;

/********************************************************************************
 *
 *   Template Stuff
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Sandkasten');
/*$messages[] = array('text' =>   'Das ist eine Spielwiese für Entwickler, '.
                                'hier kannst du dich austoben und Funktionen testen! :-)',
                                'strong' => true, 'color' => 'green');
//$messages[] = array('text' =>   '<br>ACHTUNG: Auf keinen Fall im produktiven Einsatz hier rumspielen, '.
                                'die Datenbank könnte zerstört werden!!',
                                'strong' => true, 'color' => 'red');
                                */

$t = textdomain(null);

$messages[] = array('text' =>   sprintf(_('Das ist %s!'), "english"),
    'strong' => true, 'color' => 'red');

$html->printHeader($messages);

/********************************************************************************
 *
 *   Sandbox Begin
 *
 *********************************************************************************/

// create standard objects
$database           = new Database();
$log                = new Log($database);
$system             = new System($database, $log);
$current_user       = new User($database, $current_user, $log, 1); // admin

// a PDO object
$options['PDO::MYSQL_ATTR_INIT_COMMAND'] = 'SET NAMES '.$config['db']['charset'];
$pdo = new PDO('mysql:host='.$config['db']['host'].';dbname='.$config['db']['name'],
    $config['db']['user'], $config['db']['password'], $options);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//include(BASE."/lib/bbcode/BBCodeParser.php");

$bbcode = new Golonka\BBCode\BBCodeParser;

echo $bbcode->parse('[b]Bold Text![/b]');

/*
// Insert some Footprints
print '<div class="outer"><h2>Footprints hinzufügen</h2><div class="inner">';
print '<form action="" method="post">';
if (isset($_REQUEST['insert_footprints']))
{
    $start_time = microtime(true);
    try
    {
        $trans_id = $database->begin_transaction();
        for ($i=0; $i<$_REQUEST['count']; $i++)
        {
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
            $parent = ((isset($_REQUEST['with_error'])) && ($i == ($_REQUEST['count']/2))) ? -1 : 0;

            if ($i % 3 == 0)
                $filename = '/IC_DIP02'; // correct filename, but without path and extension
            elseif ($i % 3 == 1)
                $filename = '/asdf'; // incorrect filename
            elseif ($i % 3 == 2)
                $filename = ''; // no filename

            $footprint = Footprint::add($database, $current_user, $log, $randomString, $parent, $filename);
        }
        $database->commit($trans_id);
    }
    catch (Exception $e)
    {
        $database->rollback();
        $footprint_error = $e->getMessage();
    }
    $footprint_milliseconds = (integer)(1000*(microtime(true) - $start_time));
}
print 'Anzahl Footprints in der Tabelle: '.($database->get_count_of_records('footprints')).'<br>';
print '<input type="text" size="6" name="count" value="'.((isset($_REQUEST['count'])) ? $_REQUEST['count'] : 100).'">';
print '<input type="submit" name="insert_footprints" value="Footprints hinzufügen">';
print '<input type="checkbox" name="with_error" '.(isset($_REQUEST['with_error']) ? 'checked' : '').'>Mit Fehler in der Hälfte<br>';
if (isset($footprint_milliseconds)) {print 'Zeit: '.$footprint_milliseconds.'ms<br>';}
if (isset($footprint_error)) {print '<b><font style="color:red">Fehlermeldung: '.$footprint_error.'</font></b><br>';}
print '</form></div></div>'; */


phpinfo();



//$query = 'INSERT INTO footprints (name) VALUES (?)';
//$id = $database->execute($query, array(rand(1,9999)));


//$trans_id = $database->begin_transaction();
//$id = $database->execute('UPDATE footprints SET filename=? WHERE id=?', array(rand(10000,99999), 150));
//$database->rollback();

//$query_data = $database->query('SELECT count(*) as count FROM internal');

//$count = intval($query_data[0]['count']);

//$database->commit($trans_id);

//print 'ID = '.$id.'<br>COUNT = '.$count;



//$options['PDO::MYSQL_ATTR_INIT_COMMAND'] = 'SET NAMES '.$config['db']['charset'];
//$pdo = new PDO('mysql:host='.$config['db']['host'].';dbname='.$config['db']['name'],
//                                        $config['db']['user'], $config['db']['password'], array(PDO::ATTR_PERSISTENT => true)/*, $options*/);
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//$pdo->beginTransaction();
//$pdo_statement = $pdo->prepare("UPDATE footprints SET filename=? WHERE id=?");
//$pdo_statement->bindValue(1, rand(10000,99999));
//$pdo_statement->bindValue(2, 150);
//$pdo_statement->execute();
//$pdo->exec("UPDATE footprints (filename) VALUES ('".rand(10000,99999)."') WHERE id='150'");
//$pdo->exec("INSERT INTO footprints (name) VALUES ('".rand(1000,9999)."')");
//$pdo->rollback();


/********************************************************************************
 *
 *   Sandbox End
 *
 *********************************************************************************/

$html->printFooter();
