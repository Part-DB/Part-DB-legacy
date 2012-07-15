<?php
/*
    $Id: nav.php 377 2012-02-27 23:21:10Z bubbles.red@gmail.com $
*/
    /** edit: 20120711 Udo Neist **/

    require_once ('lib.php');
    include_once ('db_update.php');

    /** end: 20120711 Udo Neist **/

    $action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');

    /** edit: 20120715 Udo Neist **/

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
    $tmpl -> setVar('head_title', $title);
    $tmpl -> setVar('head_charset', $http_charset);
    $tmpl -> setVar('head_css', $css);
    $tmpl -> setVar('head_menu', true);
    $tmpl -> pparse();

    $tmpl = new vlibTemplate(BASE."/templates/$theme/config_system.php/vlib_config_system.tmpl");
    $tmpl -> setVar('version', 'SVN '.get_svn_revision());

    $charsets = array();
    foreach (array('iso8859-1','iso8859-15','utf-8') as $charset)
    {
      $charsets[] = array('charsets'=>$charset,'checked'=>(($http_charset==$charset)?1:0));
    }
    $tmpl -> setLoop('head_charset', $charsets);
    $tmpl -> setVar('head_css', $css);

    $tmpl -> setVar('currency', $currency);

    $tmpl -> setVar('db_server', $db['mysql_server']);
    $tmpl -> setVar('db_database', $db['database']);
    $tmpl -> setVar('db_version_1', getDBVersion());
    $tmpl -> setVar('db_version_2', getDBVersion()!==getSollDBVersion());

    $tmpl -> setVar('datasheet_path', str_replace(BASE.'/','./',$datasheet_path));
    $tmpl -> setVar('use_datasheet_path', (($use_datasheet_path===true)?1:0));

    $tmpl -> setVar('disable_update_list', (($disable_update_list===true)?1:0));
    $tmpl -> setVar('disable_devices', (($disable_devices===true)?1:0));
    $tmpl -> setVar('hide_id', (($hide_id===true)?1:0));
    $tmpl -> setVar('disable_help', (($disable_help===true)?1:0));

    $tmpl -> setVar('use_modal_dialog', $use_modal_dialog);
    $tmpl -> setVar('dialog_width', $dialog_width);
    $tmpl -> setVar('dialog_height', $dialog_height);

    $tmpl -> pparse();

    $tmpl = new vlibTemplate(BASE."/templates/$theme//vlib_foot.tmpl");
    $tmpl -> pparse();

    /** end: 20120715 Udo Neist **/
?>
