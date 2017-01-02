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

    $Id$

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2012-??-??  weinbauer73         - changed to templates
        2012-09-09  kami89              - changed to OOP
*/

    /*
     * Please note:
     *  The files "edit_categories.php", "edit_footprints.php", "edit_manufacturers.php",
     *  "edit_suppliers.php", "edit_devices.php", "edit_storelocations.php" and "edit_filetypes.php"
     *  are quite similar.
     *  If you make changes in one of them, please check if you should change the other files too.
     */

    include_once('start_session.php');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

    /********************************************************************************
    *
    *   Evaluate $_REQUEST
    *
    *   Notes:
    *       - "$selected_id == 0" means that we will show the form for creating a new footprint
    *       - the $new_* variables contains the new values after editing an existing
    *           or creating a new footprint
    *
    *********************************************************************************/

    $selected_id                    = isset($_REQUEST['selected_id'])                   ? (integer)$_REQUEST['selected_id']                     : 0;
    $new_name                       = isset($_REQUEST['name'])                          ? trim((string)$_REQUEST['name'])                       : '';
    $new_parent_id                  = isset($_REQUEST['parent_id'])                     ? (integer)$_REQUEST['parent_id']                       : 0;
    $new_filename                   = isset($_REQUEST['filename'])                      ? to_unix_path(trim((string)$_REQUEST['filename']))     : '';
    $new_3d_filename                = isset($_REQUEST['filename_3d'])                   ? to_unix_path(trim((string)$_REQUEST['filename_3d']))  : '';

    if ((strlen($new_filename) > 0) && ( ! is_path_absolute_and_unix($new_filename)))
        $new_filename = BASE.'/'.$new_filename; // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

    if ((strlen($new_3d_filename) > 0) && ( ! is_path_absolute_and_unix($new_3d_filename)))
        $new_3d_filename = BASE.'/'.$new_3d_filename; // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

    $add_more                       = isset($_REQUEST['add_more']);

    $broken_footprints_count        = isset($_REQUEST['broken_footprints_count'])       ? (integer)$_REQUEST['broken_footprints_count']     : 0;
    $save_all_proposed_filenames    = isset($_REQUEST["save_all_proposed_filenames"]);

    $broken_3d_footprints_count     = isset($_REQUEST['broken_3d_footprints_count'])       ? (integer)$_REQUEST['broken_3d_footprints_count']     : 0;
    $save_all_proposed_3d_filenames = isset($_REQUEST["save_all_proposed_3d_filenames"]);

    $action = 'default';
    if (isset($_REQUEST["add"]))                            {$action = 'add';}
    if (isset($_REQUEST["delete"]))                         {$action = 'delete';}
    if (isset($_REQUEST["delete_confirmed"]))               {$action = 'delete_confirmed';}
    if (isset($_REQUEST["apply"]))                          {$action = 'apply';}
    if (isset($_REQUEST["save_proposed_filenames"]))        {$action = 'save_proposed_filenames';}
    if (isset($_REQUEST["save_all_proposed_filenames"]))    {$action = 'save_proposed_filenames';}
    if (isset($_REQUEST["save_proposed_3d_filenames"]))     {$action = 'save_proposed_3d_filenames';}
    if (isset($_REQUEST["save_all_proposed_3d_filenames"])) {$action = 'save_proposed_3d_filenames';}


    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Footprints'));

    try
    {
        $database           = new Database();
        $log                = new Log($database);
        $current_user       = new User($database, $current_user, $log, 1); // admin
        $root_footprint     = new Footprint($database, $current_user, $log, 0);

        if ($selected_id > 0)
            $selected_footprint = new Footprint($database, $current_user, $log, $selected_id);
        else
            $selected_footprint = NULL;
    }
    catch (Exception $e)
    {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }

    /********************************************************************************
    *
    *   Execute actions
    *
    *********************************************************************************/

    if ( ! $fatal_error)
    {
        switch ($action)
        {
            case 'add':
                try
                {
                    $new_footprint = Footprint::add($database, $current_user, $log, $new_name,
                                                    $new_parent_id, $new_filename, $new_3d_filename);

                    if ( ! $add_more)
                    {
                        $selected_footprint = $new_footprint;
                        $selected_id = $selected_footprint->get_id();
                    }
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => _('Der neue Footprint konnte nicht angelegt werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'delete':
                try
                {
                    if ( ! is_object($selected_footprint))
                        throw new Exception(_('Es ist kein Footprint markiert oder es trat ein Fehler auf!'));

                    $parts = $selected_footprint->get_parts();
                    $count = count($parts);

                    if ($count > 0)
                    {
                        $messages[] = array('text' => sprintf(_('Es gibt noch %d Bauteile mit diesem Footprint, '.
                                            'daher kann der Footprint nicht gelöscht werden.'), $count), 'strong' => true, 'color' => 'red');
                    }
                    else
                    {
                        $messages[] = array('text' => sprintf(_('Soll der Footprint "%s'.
                                                        '" wirklich unwiederruflich gelöscht werden?'), $selected_footprint->get_full_path()), 'strong' => true, 'color' => 'red');
                        $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                        $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Es gibt keine Bauteile mit diesem Footprint.'));
                        $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Beinhaltet dieser Footprint noch Unterfootprints, dann werden diese eine Ebene nach oben verschoben.'));
                        $messages[] = array('html' => '<input type="hidden" name="selected_id" value="'.$selected_footprint->get_id().'">');
                        $messages[] = array('html' => '<input type="submit" class="btn btn-default" name="" value="'._('Nein, nicht löschen').'">', 'no_linebreak' => true);
                        $messages[] = array('html' => '<input type="submit" class="btn btn-danger" name="delete_confirmed" value="'._('Ja, Footprint löschen').'">');
                    }
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => _('Es trat ein Fehler auf!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'delete_confirmed':
                try
                {
                    if ( ! is_object($selected_footprint))
                        throw new Exception(_('Es ist kein Footprint markiert oder es trat ein Fehler auf!'));

                    $selected_footprint->delete();
                    $selected_footprint = NULL;
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => _('Der Footprint konnte nicht gelöscht werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'apply':
                try
                {
                    if ( ! is_object($selected_footprint))
                        throw new Exception(_('Es ist kein Footprint markiert oder es trat ein Fehler auf!'));

                    $selected_footprint->set_attributes(array(  'name'          => $new_name,
                                                                'parent_id'     => $new_parent_id,
                                                                'filename'      => $new_filename,
                                                                'filename_3d'   => $new_3d_filename));
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'save_proposed_filenames':
                $errors = array();
                for ($i=0; $i < $broken_footprints_count; $i++)
                {
                    $spf_footprint_id   = isset($_REQUEST['broken_footprint_id_'.$i])  ? $_REQUEST['broken_footprint_id_'.$i] : -1; // -1 will produce an error
                    $spf_new_filename   = isset($_REQUEST['proposed_filename_'.$i])    ? to_unix_path($_REQUEST['proposed_filename_'.$i])   : NULL;
                    $spf_checked        = isset($_REQUEST['filename_checkbox_'.$i])     || $save_all_proposed_filenames;

                    if ((strlen($spf_new_filename) > 0) && (! is_path_absolute_and_unix($spf_new_filename)))
                        $spf_new_filename = BASE.'/'.$spf_new_filename; // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

                    try
                    {
                        if ($spf_checked)
                        {
                            $spf_broken_footprint = new Footprint($database, $current_user, $log, $spf_footprint_id);
                            $spf_broken_footprint->set_filename($spf_new_filename);
                        }
                    }
                    catch (Exception $e)
                    {
                        $errors[] = $e->getMessage();
                    }
                }

                foreach ($errors as $error)
                    $messages[] = array('text' => _('Fehlermeldung: ').$error, 'color' => 'red');

                break;

            case 'save_proposed_3d_filenames':
                $errors = array();
                for ($i=0; $i < $broken_3d_footprints_count; $i++)
                {
                    $spf_footprint_id   = isset($_REQUEST['broken_3d_footprint_id_'.$i])  ? $_REQUEST['broken_3d_footprint_id_'.$i] : -1; // -1 will produce an error
                    $spf_new_filename   = isset($_REQUEST['proposed_3d_filename_'.$i])    ? to_unix_path($_REQUEST['proposed_3d_filename_'.$i])   : NULL;
                    $spf_checked        = isset($_REQUEST['filename_3d_checkbox_'.$i])     || $save_all_proposed_3d_filenames;

                    if ((strlen($spf_new_filename) > 0) && (! is_path_absolute_and_unix($spf_new_filename)))
                        $spf_new_filename = BASE.'/'.$spf_new_filename; // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

                    try
                    {
                        if ($spf_checked)
                        {
                            $spf_broken_footprint = new Footprint($database, $current_user, $log, $spf_footprint_id);
                            $spf_broken_footprint->set_3d_filename($spf_new_filename);
                        }
                    }
                    catch (Exception $e)
                    {
                        $errors[] = $e->getMessage();
                    }
                }

                foreach ($errors as $error)
                    $messages[] = array('text' => 'Fehlermeldung: '.$error, 'color' => 'red');

                break;
        }
    }

    /********************************************************************************
    *
    *   List broken filename footprints
    *
    *********************************************************************************/

    if (! $fatal_error)
    {
        try
        {
            $broken_filename_footprints = Footprint::get_broken_filename_footprints($database, $current_user, $log);
            $broken_filename_loop = array();

            if (count($broken_filename_footprints) > 0)
            {
                // get all available files for the proposed footprint images
                $available_proposed_files = array_merge(find_all_files(BASE.'/img/', true), find_all_files(BASE.'/data/media/', true));

                // read the PHP constant "max_input_vars"
                $max_input_vars = ((ini_get('max_input_vars') !== false) ? (int)ini_get('max_input_vars') : 999999);

                for ($i=0; $i < count($broken_filename_footprints); $i++)
                {
                    // avoid too many post variables
                    if ($i*10 >= $max_input_vars)
                        break;

                    // avoid too long execution time and a huge HTML table
                    if ($i >= 100)
                        break;

                    $footprint = $broken_filename_footprints[$i];
                    $proposed_filenames_loop = array();
                    $proposed_filenames = get_proposed_filenames($footprint->get_filename(), $available_proposed_files);

                    if ((count($proposed_filenames) > 0) && (pathinfo($proposed_filenames[0], PATHINFO_FILENAME) == pathinfo($footprint->get_filename(), PATHINFO_FILENAME)))
                        $exact_match = true;
                    else
                        $exact_match = false;

                    foreach ($proposed_filenames as $index => $filename)
                    {
                        $filename = str_replace(BASE.'/', '', $filename);
                        $proposed_filenames_loop[] = array( 'selected' => (($index == 0) && $exact_match),
                                                            'proposed_filename' => $filename);
                    }

                    $broken_filename_loop[] = array(    'index'                     => $i,
                                                        'checked'                   => $exact_match,
                                                        'broken_id'                 => $footprint->get_id(),
                                                        'broken_full_path'          => $footprint->get_full_path(),
                                                        'broken_filename'           => str_replace(BASE.'/', '', $footprint->get_filename()),
                                                        'proposed_filenames_count'  => count($proposed_filenames_loop),
                                                        'proposed_filenames'        => $proposed_filenames_loop);
                }

                $html->set_loop('broken_filename_footprints', $broken_filename_loop);
            }

            $html->set_variable('broken_footprints_count', count($broken_filename_loop), 'integer');
            $html->set_variable('broken_footprints_count_total', count($broken_filename_footprints), 'integer');
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => 'Es konnten nicht alle Footprints mit defektem Dateinamen aufgelistet werden!',
                                'strong' => true, 'color' => 'red');
            $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
        }
    }

    /********************************************************************************
     *
     *   List broken 3d filename footprints
     *
     *********************************************************************************/

    if (! $fatal_error)
    {
        try
        {
            $broken_filename_footprints = Footprint::get_broken_3d_filename_footprints($database, $current_user, $log);
            $broken_filename_loop = array();

            if (count($broken_filename_footprints) > 0)
            {
                // get all available files for the proposed footprint images
                $available_proposed_files = array_merge(find_all_files(BASE.'/models/', true));

                // read the PHP constant "max_input_vars"
                $max_input_vars = ((ini_get('max_input_vars') !== false) ? (int)ini_get('max_input_vars') : 999999);

                for ($i=0; $i < count($broken_filename_footprints); $i++)
                {
                    // avoid too many post variables
                    if ($i*10 >= $max_input_vars)
                        break;

                    // avoid too long execution time and a huge HTML table
                    if ($i >= 100)
                        break;

                    $footprint = $broken_filename_footprints[$i];
                    $proposed_filenames_loop = array();
                    $proposed_filenames = get_proposed_filenames($footprint->get_3d_filename(), $available_proposed_files);

                    if ((count($proposed_filenames) > 0) && (pathinfo($proposed_filenames[0], PATHINFO_FILENAME) == pathinfo($footprint->get_3d_filename(), PATHINFO_FILENAME)))
                        $exact_match = true;
                    else
                        $exact_match = false;

                    foreach ($proposed_filenames as $index => $filename)
                    {
                        $filename = str_replace(BASE.'/', '', $filename);
                        $proposed_filenames_loop[] = array( 'selected' => (($index == 0) && $exact_match),
                                                            'proposed_filename' => $filename);
                    }

                    $broken_filename_loop[] = array(    'index'                     => $i,
                                                        'checked'                   => $exact_match,
                                                        'broken_id'                 => $footprint->get_id(),
                                                        'broken_full_path'          => $footprint->get_full_path(),
                                                        'broken_filename'           => str_replace(BASE.'/', '', $footprint->get_3d_filename()),
                                                        'proposed_filenames_count'  => count($proposed_filenames_loop),
                                                        'proposed_filenames'        => $proposed_filenames_loop);
                }

                $html->set_loop('broken_3d_filename_footprints', $broken_filename_loop);
            }

            $html->set_variable('broken_3d_footprints_count', count($broken_filename_loop), 'integer');
            $html->set_variable('broken_3d_footprints_count_total', count($broken_filename_footprints), 'integer');
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => 'Es konnten nicht alle Footprints mit defektem Dateinamen aufgelistet werden!',
                                'strong' => true, 'color' => 'red');
            $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
        }
    }


    /********************************************************************************
    *
    *   Set the rest of the HTML variables
    *
    *********************************************************************************/

    $html->set_variable('add_more', $add_more, 'boolean');

    if (! $fatal_error)
    {
        try
        {
            if (is_object($selected_footprint))
            {
                $parent_id = $selected_footprint->get_parent_id();
                $html->set_variable('id', $selected_footprint->get_id(), 'integer');
                $name = $selected_footprint->get_name();
                $filename = $selected_footprint->get_filename();
                $filename_3d = $selected_footprint->get_3d_filename();
            }
            elseif ($action == 'add')
            {
                $parent_id = $new_parent_id;
                $name = $new_name;
                $filename = $new_filename;
                $filename_3d = $new_3d_filename;
            }
            else
            {
                $parent_id = 0;
                $name = '';
                $filename = '';
                $filename_3d = '';
            }

            $html->set_variable('name', $name, 'string');
            $html->set_variable('filename', str_replace(BASE.'/', '', $filename), 'string');

            $html->set_variable('filename_3d', str_replace(BASE.'/', '', $filename_3d), 'string');
            $html->set_variable('foot3d_active', $config['foot3d']['active'],'boolean');

            //Say if file is valid (needed for preview in footprints)
            if (is_object($selected_footprint))
            {
                $html->set_variable('filename_3d_valid', $selected_footprint->is_3d_filename_valid(), 'boolean');
                $html->set_variable('filename_valid', $selected_footprint->is_filename_valid(), 'boolean');
            }

            $footprint_list = $root_footprint->build_html_tree($selected_id, true, false);
            $html->set_variable('footprint_list', $footprint_list, 'string');

            $parent_footprint_list = $root_footprint->build_html_tree($parent_id, true, true);
            $html->set_variable('parent_footprint_list', $parent_footprint_list, 'string');
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
            $fatal_error = true;
        }
    }

    /********************************************************************************
    *
    *   Generate HTML Output
    *
    *********************************************************************************/

    $reload_link = $fatal_error ? 'edit_footprints.php' : '';    // an empty string means that the...
    $html->print_header($messages, $reload_link);                // ...reload-button won't be visible

    if (! $fatal_error)
        $html->print_template('edit_footprints');

    $html->print_footer();
