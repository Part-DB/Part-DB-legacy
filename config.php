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

    $Id: config.php 511 2012-08-04 weinbauer73@gmail.com $
*/

/*  start session */
session_name ("Part-DB");
session_start();

/* set base and search path */
if (!$_SESSION['part-db']['base'])
{
$_SESSION['part-db']['base'] = getcwd();
}
define("BASE",$_SESSION['part-db']['base']);

set_include_path(get_include_path().PATH_SEPARATOR.BASE.implode(PATH_SEPARATOR.BASE,array('/class','/class/vlib','')));

/* load classes */
require ('vlibTemplate.php');
require ('vlibDate.php');
require ('vlibMimeMail.php');

/* define theme, e.g. standard, Greenway */
$conf['html']['theme'] = "standard";
/* if you want to use an alternative css named css/$theme.css, set this to true */
$conf['html']['css'] = false;
/* set charset for web pages (empty for none, ISO-8859-1, utf-8) */
$conf['html']['http_charset'] = "utf-8"; // Default geändert: Udo Neist 20120705

/* set internal encoding to http_charset */
mb_internal_encoding($conf['html']['http_charset']);

/* set timezone (e.g. Europe/Berlin) */
date_default_timezone_set("Europe/Berlin");

/* set language */
define('LANGUAGE','de_DE');
setlocale(LC_ALL, LANGUAGE);
/* use this instead of $currency! */
setlocale(LC_MONETARY, LANGUAGE);
$currency_format = array(
    'de_DE'=>'%=*^-14#6.2i',
    'en_US'=>'%i',
    'en_GB'=>'%i',
    'it_IT'=>'%.2n'
);
$currency_format = $currency_format[LANGUAGE];

/* set version */
$conf['version']['author'] = 'Udo Neist';
$conf['version']['build'] = '20120829';
$conf['version']['string'] = ' (modified by '.$conf['version']['author'].', Build: '.$conf['version']['build'].')';

/* set system variables, e.g. email of an administrator or master user */
$conf['sys']['email'] = '';

/* load database configuration */
include ('config_db.php');

/** obsolete! use setlocale(LC_MONETARY, 'de_DE') and $currency_format **/
$currency  = "&euro;";

/* set your own title here, and prevent it from updates */
$title = "PART-DB Elektronische Bauteile-Datenbank";
$startup_title = "Part-DB V0.2.2";

/* disable the update list on the startup page (e.g. for standalone systems without internet)*/
$disable_update_list = true;

/* disable devices function, in case you don't need it */
$disable_devices = false;

/* disable help (it's not useful for multiuser environments) */
$disable_help = false;

/* disable Config (it's not useful for multiuser environments) */
$disable_config = false;

/* default for common datasheet path */
$use_datasheet_path = false;

/* common (e.g. on server) datasheet path */
$datasheet_path = BASE.'/datasheets/';

/* hide the id in table views */
$hide_id = false;

/* hide minimum in stock in table views */
$hide_mininstock = false;

/* set size and type of dialog */
$use_modal_dialog = true;
$dialog_width = 680;
$dialog_height= 400;

/* add your individual banner here: */
$banner = <<<BANNER
<!--

<div class="outer">
<h2>Banner</h2>
<div class="inner">
Willkommen zur Part-DB!
</div>
</div>
-->
BANNER;
?>
