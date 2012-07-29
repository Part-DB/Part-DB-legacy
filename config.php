<?php

    /** new: 20120617 Udo Neist **/

    /*  start session */
    session_name ("Part-DB");
    session_start();

    /** edit: 20120711 Udo Neist **/

    /* set base and search path */
    if (!$_SESSION['part-db']['base'])
    {
        $_SESSION['part-db']['base'] = getcwd();
    }
    define("BASE",$_SESSION['part-db']['base']);

    /** end: 20120711 Udo Neist **/

    set_include_path(get_include_path().PATH_SEPARATOR.BASE.implode(PATH_SEPARATOR.BASE,array('/class','/class/vlib','')));

    /* load classes */
    require ('vlibTemplate.php');
    require ('vlibDate.php');
    require ('vlibMimeMail.php');

    /** edit: 20120720 Udo Neist **/

    /* define theme, e.g. standard, Greenway */
    $theme = "standard";
    /* if an alternative css named css/$theme.css exists,
       it will used additional to the standard theme partdb.css */
    $css = ((is_readable(BASE."/css/$theme.css"))?"css/$theme.css":"templates/$theme/partdb.css");

    /** end: 20120720 Udo Neist **/

    /** end: 20120617 Udo Neist **/

    /** new: 20120705 Udo Neist **/

    /* set timezone (e.g. Europe/Berlin) */
    date_default_timezone_set("Europe/Berlin");

    /* set language */
    define(LANGUAGE,'de_DE');
    setlocale(LC_ALL, LANGUAGE);

    /** end: 20120705 Udo Neist **/

    /** new: 20120624 Udo Neist **/

    /* set version */
    $conf['version']['author'] = 'Udo Neist';
    $conf['version']['build'] = '20120729';
    $conf['version']['string'] = ' (modified by '.$conf['version']['author'].', Build: '.$conf['version']['build'].')';

    /* load database configuration */
    include ('config_db.php');

    /** end: 20120624 Udo Neist **/

    /** other config variables **/

    /* choose your currency */
    $currency      = "&euro;";

    /* set charset for web pages (empty for none, ISO-8859-1, utf-8) */
    $http_charset  = "utf-8"; // Default geändert: Udo Neist 20120705

    /** edit: 20120711 Udo Neist **/

    /* set internal encoding to $http_charset */
    mb_internal_encoding($http_charset);

    /** end: 20120711 Udo Neist **/

    /* set your own title here, and prevent it from updates */
    $title         = "PART-DB Elektronische Bauteile-Datenbank";
    $startup_title = "Part-DB V0.2.1";

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

    /** edit: 20120711 Udo Neist **/

    /* common (e.g. on server) datasheet path */
    $datasheet_path = BASE.'/datasheets/';

    /* backup path for database backups*/
    $db_backup_path = "backup/";

    /** end: 20120711 Udo Neist **/

    /* hide the id in table views */
    $hide_id = false;

    /* hide minimum in stock in table views */
    $hide_mininstock = false;

    /* set size and type of dialog */
    $use_modal_dialog = true;
    $dialog_width     = 680;
    $dialog_height    = 400;

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
