<?php
/*
    $Id: config_page.php 479 2012-07-03 17:43:42Z kami89@gmx.ch $
*/

    /** edit: 20120711 Udo Neist **/

    require_once ('lib.php');
    include_once ('db_update.php');

    /** end: 20120711 Udo Neist **/

    function list_backup_files( $path)
    {
        $handle = opendir($path);

        while($file = readdir($handle))
        {
                $file_array[] = $file;
        }

        rsort($file_array);

        /** edit: 20120711 Udo Neist **/

        $options = array();

        foreach($file_array as $file)
        {
            if (($file != '.') && ($file != '..') && ($file != '.svn') && (!(is_dir($path.$file))))
            {
                $options[]['db_backup_filename'] = smart_unescape($file);

            }
        }


        closedir($handle);

        return $options;

        /** end: 20120711 Udo Neist **/

    }

    $action = 'default';
    if ( isset( $_REQUEST["db_update"]) )          { $action = 'db_update';}
    if ( isset( $_REQUEST["set_auto_update"]) )    { $action = 'set_auto_update';}
    if ( isset( $_REQUEST["backup"]) )             { $action = 'backup';}
    if ( isset( $_REQUEST["download_file"]) )      { $action = 'download_file';}
    if ( isset( $_REQUEST["delete_file"]) )        { $action = 'delete_file';}

    $selected_backup_file = isset( $_REQUEST["selected_backup_file"]) ? $_REQUEST["selected_backup_file"] : "";

    /** edit: 20120711 Udo Neist **/

    $backup_path = isset($db['backup_path']) ? $db['backup_path'] : "backup/";

    /** end: 20120711 Udo Neist **/


    if ($action == "backup")
    {
        /** edit: 20120711 Udo Neist **/

        $backup_file = $db['database'].'_'. date("Y-m-d_H:i:s") . '.sql';
        $command = "mysqldump --opt -h ".$db['mysql_server']." -u".$db['user']." -p".$db['password']." ".$db['database']." > $backup_path$backup_file";

        /** end: 20120711 Udo Neist **/

        exec($command);
    }

    if ($action == "download_file")
    {
        if ($selected_backup_file != "")
        {
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=".$selected_backup_file);
            header("Content-Length:". filesize($backup_path.$selected_backup_file));

            readfile($backup_path.$selected_backup_file);
        }
    }

    if ($action == "delete_file")
    {
        if ($selected_backup_file != "")
        {
            $command = "rm $backup_path$selected_backup_file";

            exec($command);
        }
    }

    /** edit: 20120715 Udo Neist **/

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_head.tmpl");
    $tmpl -> setVar('head_title', $title);
    $tmpl -> setVar('head_charset', $http_charset);
    $tmpl -> setVar('head_theme', $theme);
    $tmpl -> setVar('head_css', $css);
    $tmpl -> setVar('head_menu', true);
    $tmpl -> pparse();

    $tmpl = new vlibTemplate(BASE."/templates/$theme/config_page.php/vlib_config_page.tmpl");
    $tmpl -> setVar('db_version_1', getDBVersion());
    $tmpl -> setVar('db_version_2', getSollDBVersion());
    $tmpl -> setVar('db_version_update', checkDBUpdateNeeded());
    $tmpl -> setVar('db_version_update_log', (($action == 'db_update')?doDBUpdate():''));
    if ($action == "set_auto_update")
    {
        $tmpl -> setVar('db_version_autoupdate_set', setDBAutomaticUpdateActive($_REQUEST["active"]=='active'));
    }
    $tmpl -> setVar('db_version_autoupdate_check', ((getDBAutomaticUpdateActive())?'checked':''));

    $tmpl -> setLoop('db_list_backups', list_backup_files($backup_path));
    $tmpl -> pparse();

    $tmpl = new vlibTemplate(BASE."/templates/$theme/vlib_foot.tmpl");
    $tmpl -> pparse();

    /** end: 20120715 Udo Neist **/

?>
