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
    *   Footprint Sortiments
    *
    *********************************************************************************/

    // the common footprints
    $common_footprints = array(
        array('text' => 'Widerstände', 'root_name' => 'Widerstände', 'root_image' => '',
            'tht' => array(
                array('name' => '0204',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-KOHLE_0204.png'),
                array('name' => '0207',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-KOHLE_0207.png'),
                array('name' => '0309',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-KOHLE_0309.png'),
                array('name' => '0414',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-KOHLE_0414.png'),
                array('name' => '0617',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-KOHLE_0617.png'),
                array('name' => '0922',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-KOHLE_0922.png')
            ),
            'smt' => array(
                array('name' => '0102 MICRO-MELF',  'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0102-MLF.png'),
                array('name' => '0204 MINI-MELF',   'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0204-MLF.png'),
                array('name' => '0207 MELF',        'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0207-MLF.png'),
                array('name' => '0402',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0402.png'),
                array('name' => '0603',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0603.png'),
                array('name' => '0805',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0805.png'),
                array('name' => '1206',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_1206.png'),
                array('name' => '1210',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_1210.png'),
                array('name' => '1218',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_1218.png'),
                array('name' => '2010',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_2010.png'),
                array('name' => '2512',             'image' => '/img/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_2512.png')
            )
        ),
        array('text' => 'Kondensatoren', 'root_name' => 'Kondensatoren', 'root_image' => '',
            'tht' => array(
                array('name' => 'AXIAL', 'image' => '',
                    'footprints' => array(
                        array('name' => 'RM=22.86mm D=10mm', 'image' => '') // TODO
                    )
                )
                array('name' => 'RADIAL', 'image' => '',
                    'footprints' => array(
                        array('name' => 'RM=2mm D=5mm',     'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_2RM_5D.png'),
                        array('name' => 'RM=2.5mm D=5mm',   'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_2RM5_5D.png'),
                        array('name' => 'RM=2.5mm D=6.3mm', 'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_2RM5_6D3.png'),
                        array('name' => 'RM=3.5mm D=8mm',   'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_3RM5_8D.png'),
                        array('name' => 'RM=5mm D=10mm',    'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_5RM_10D.png'),
                        array('name' => 'RM=5mm D=12.5mm',  'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_5RM_12D5.png'),
                        array('name' => 'RM=7.5mm D=16mm',  'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_7RM5_16D.png'),
                        array('name' => 'RM=7.5mm D=18mm',  'image' => '/img/footprints/Passiv/Kondensatoren/Elektrolyt/Radial/ELKO_RADIAL_7RM5_18D.png')
                    )
                )
            ),
            'smt' => array(
                array('name' => 'Elektrolyth', 'image' => '',
                    'footprints' => array(
                        array('name' => 'RM=22.86mm D=10mm', 'image' => '') // TODO
                    )
                )
            )
        ),






        array('text' => 'Induktivitäten / Spulen',                'tht' => true,  'smt' => true),
        array('text' => 'Transistoren / FETs',                    'tht' => true,  'smt' => true),
        array('text' => 'Dioden',                                 'tht' => true,  'smt' => true),
        array('text' => 'Quarze / Resonatoren / Oszillatoren',    'tht' => true,  'smt' => true),
        array('text' => 'LEDs',                                   'tht' => true,  'smt' => true),
        array('text' => 'Potentiometer / Trimmer',                'tht' => true,  'smt' => true),
        array('text' => 'DIP-Switches',                           'tht' => true,  'smt' => true),
        array('text' => 'Schalter / Taster',                      'tht' => true,  'smt' => true),
        array('text' => 'Stift-/Buchsenleisten',                  'tht' => true,  'smt' => array()),
        array('text' => 'Schraubklemmen',                         'tht' => true,  'smt' => array()),
        array('text' => 'IC-Sockel DIP',                          'tht' => true,  'smt' => array()),
        array('text' => 'Gehäuse: DIP',                           'tht' => true,  'smt' => array()),
        array('text' => 'Gehäuse: *QFP (TQFP, LQFP, ...)',        'tht' => array(), 'smt' => true),
        array('text' => 'Gehäuse: *SOP/SOT (SSOP, TSOP, ...)',    'tht' => array(), 'smt' => true),
        array('text' => 'Gehäuse: *GA (LGA, BGA, ...)',           'tht' => array(), 'smt' => true),
        array('text' => 'Gehäuse: PLCC (IC und Sockel)',          'tht' => array(), 'smt' => true),
        array('text' => 'Mechanik: Kühlkörper',                   'tht' => true,  'smt' => array())
    );

    // function to build a template loop array from the first level of $common_footprints
    function build_sortiment_loop($footprints)
    {
        $loop = array();

        foreach ($footprints as $key => $footprint)
        {
            $loop[] = array('index'     => $key,
                            'text'      => $footprint['text'],
                            'tht_count' => count($footprint['tht']),
                            'smt_count' => count($footprint['smt']));
        }

        return $loop;
    }

    /********************************************************************************
    *
    *   Evaluate $_REQUEST
    *
    *   Notes:
    *       - "$selected_id == 0" means that we will show the form for creating a new footprint
    *       - "$selected_id == -1" means that we will show the form for creating a lot of common footprints (sortiments)
    *       - the $new_* variables contains the new values after editing an existing
    *           or creating a new footprint
    *
    *********************************************************************************/

    $selected_id                    = isset($_REQUEST['selected_id'])                   ? (integer)$_REQUEST['selected_id']                 : 0;
    $new_name                       = isset($_REQUEST['name'])                          ? trim((string)$_REQUEST['name'])                   : '';
    $new_parent_id                  = isset($_REQUEST['parent_id'])                     ? (integer)$_REQUEST['parent_id']                   : 0;
    $new_filename                   = isset($_REQUEST['filename'])                      ? to_unix_path(trim((string)$_REQUEST['filename'])) : '';
    $add_more                       = isset($_REQUEST['add_more']);

    if ((strlen($new_filename) > 0) && ( ! is_path_absolute_and_unix($new_filename)))
        $new_filename = BASE.'/'.$new_filename; // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

    // footprint sortiments
    $sortiment_overwrite_existing   = isset($_REQUEST['sortiment_overwrite_existing']);
    $sortiment_use_hierarchy        = isset($_REQUEST['sortiment_use_hierarchy']);

    // broken footprints
    $broken_footprints_count        = isset($_REQUEST['broken_footprints_count'])       ? (integer)$_REQUEST['broken_footprints_count']     : 0;
    $save_all_proposed_filenames    = isset($_REQUEST["save_all_proposed_filenames"]);

    $action = 'default';
    if (isset($_REQUEST["add"]))                            {$action = 'add';}
    if (isset($_REQUEST["delete"]))                         {$action = 'delete';}
    if (isset($_REQUEST["delete_confirmed"]))               {$action = 'delete_confirmed';}
    if (isset($_REQUEST["apply"]))                          {$action = 'apply';}
    if (isset($_REQUEST["save_proposed_filenames"]))        {$action = 'save_proposed_filenames';}
    if (isset($_REQUEST["save_all_proposed_filenames"]))    {$action = 'save_proposed_filenames';}
    if (isset($_REQUEST["create_sortiments"]))              {$action = 'create_sortiments';}

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Footprints');

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
                                                    $new_parent_id, $new_filename);

                    if ( ! $add_more)
                    {
                        $selected_footprint = $new_footprint;
                        $selected_id = $selected_footprint->get_id();
                    }
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => 'Der neue Footprint konnte nicht angelegt werden!', 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'delete':
                try
                {
                    if ( ! is_object($selected_footprint))
                        throw new Exception('Es ist kein Footprint markiert oder es trat ein Fehler auf!');

                    $parts = $selected_footprint->get_parts();
                    $count = count($parts);

                    if ($count > 0)
                    {
                        $messages[] = array('text' => 'Es gibt noch '.$count.' Bauteile mit diesem Footprint, '.
                                            'daher kann der Footprint nicht gelöscht werden.', 'strong' => true, 'color' => 'red');
                    }
                    else
                    {
                        $messages[] = array('text' => 'Soll der Footprint "'.$selected_footprint->get_full_path().
                                                        '" wirklich unwiederruflich gelöscht werden?', 'strong' => true, 'color' => 'red');
                        $messages[] = array('text' => '<br>Hinweise:', 'strong' => true);
                        $messages[] = array('text' => '&nbsp;&nbsp;&bull; Es gibt keine Bauteile mit diesem Footprint.');
                        $messages[] = array('text' => '&nbsp;&nbsp;&bull; Beinhaltet dieser Footprint noch Unterfootprints, dann werden diese eine Ebene nach oben verschoben.');
                        $messages[] = array('html' => '<input type="hidden" name="selected_id" value="'.$selected_footprint->get_id().'">');
                        $messages[] = array('html' => '<input type="submit" name="" value="Nein, nicht löschen">', 'no_linebreak' => true);
                        $messages[] = array('html' => '<input type="submit" name="delete_confirmed" value="Ja, Footprint löschen">');
                    }
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => 'Es trat ein Fehler auf!', 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'delete_confirmed':
                try
                {
                    if ( ! is_object($selected_footprint))
                        throw new Exception('Es ist kein Footprint markiert oder es trat ein Fehler auf!');

                    $selected_footprint->delete();
                    $selected_footprint = NULL;
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => 'Der Footprint konnte nicht gelöscht werden!', 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'apply':
                try
                {
                    if ( ! is_object($selected_footprint))
                        throw new Exception('Es ist kein Footprint markiert oder es trat ein Fehler auf!');

                    $selected_footprint->set_attributes(array(  'name'          => $new_name,
                                                                'parent_id'     => $new_parent_id,
                                                                'filename'      => $new_filename));
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => 'Die neuen Werte konnten nicht gespeichert werden!', 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
                }
                break;

            case 'save_proposed_filenames':
                $errors = array();
                for ($i=0; $i < $broken_footprints_count; $i++)
                {
                    $spf_footprint_id   = isset($_REQUEST['broken_footprint_id_'.$i])  ? $_REQUEST['broken_footprint_id_'.$i] : -1; // -1 will produce an error
                    $spf_new_filename   = isset($_REQUEST['proposed_filename_'.$i])    ? to_unix_path($_REQUEST['proposed_filename_'.$i])   : NULL;
                    $spf_checked        = isset($_REQUEST['filename_checkbox_'.$i]) || $save_all_proposed_filenames;

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
                    $messages[] = array('text' => 'Fehlermeldung: '.$error, 'color' => 'red');

                breaK;

            case 'create_sortiments':
                try
                {
                    // todo
                    //$sortiment_overwrite_existing
                    //$sortiment_use_hierarchy
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => 'Die neuen Footprints konnten nicht angelegt werden!', 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
                }
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
    *   Set the rest of the HTML variables
    *
    *********************************************************************************/

    if (! $fatal_error)
    {
        try
        {
            $html->set_variable('add_more', $add_more, 'boolean');

            $html->set_loop('sortiment_loop', build_sortiment_loop($common_footprints));

            if (is_object($selected_footprint))
            {
                // show the footprint $selected_footprint
                $html->set_variable('id', $selected_footprint->get_id(), 'integer');
                $parent_id = $selected_footprint->get_parent_id();
                $name = $selected_footprint->get_name();
                $filename = $selected_footprint->get_filename();
            }
            elseif ($action == 'add')
            {
                // show the "add footprint" form with the data from the last created footprint
                $parent_id = $new_parent_id;
                $name = $new_name;
                $filename = $new_filename;
            }
            elseif (($selected_id == -1) /*&& ($action != 'create_sortiments')*/)
            {
                // show the "add common footprint sortiments" form
                $html->set_variable('id', '-1', 'integer');
                $parent_id = 0;
                $name = '';
                $filename = '';
            }
            else
            {
                // show the "add footprint" form with empty input boxes (this is the default case)
                $parent_id = 0;
                $name = '';
                $filename = '';
            }

            $html->set_variable('name', $name, 'string');
            $html->set_variable('filename', str_replace(BASE.'/', '', $filename), 'string');

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

?>
