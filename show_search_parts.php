<?PHP
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
        2012-10-10  kami89              - changed to OOP
*/

    include_once('start_session.php');
    include_once(BASE.'/lib/lib.export.php');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

    /********************************************************************************
    *
    *   Evaluate $_REQUEST
    *
    *********************************************************************************/

    $part_id                = isset($_REQUEST['selected_id'])       ? (integer)$_REQUEST['selected_id']     : 0;
    $keyword                = isset($_REQUEST['keyword'])           ? trim((string)$_REQUEST['keyword'])    : '';
    $search_name            = isset($_REQUEST['search_name']);
    $search_category        = isset($_REQUEST['search_category']);
    $search_description     = isset($_REQUEST['search_description']);
    $search_comment         = isset($_REQUEST['search_comment']);
    $search_supplier        = isset($_REQUEST['search_supplier']);
    $search_supplierpartnr  = isset($_REQUEST['search_supplierpartnr']);
    $search_storelocation   = isset($_REQUEST['search_storelocation']);
    $search_footprint       = isset($_REQUEST['search_footprint']);
    $search_manufacturer    = isset($_REQUEST['search_manufacturer']);
    $table_rowcount         = isset($_REQUEST['table_rowcount'])    ? (integer)$_REQUEST['table_rowcount']  : 0;

    $export_format_id       = isset($_REQUEST['export_format'])     ? (integer)$_REQUEST['export_format']   : 0;

    $disable_pid_input      = isset($_REQUEST['disable_pid_input']);

    $action = 'default';
    if (isset($_REQUEST['export']))     {$action = 'export';}
    $selected_part_id = 0;
    for($i=0; $i<$table_rowcount; $i++)
    {
        if (isset($_REQUEST['decrement_'.$i]))
        {
            $action = 'decrement';
            $selected_part_id = isset($_REQUEST['id_'.$i]) ? (integer)$_REQUEST['id_'.$i] : 0;
        }

        if (isset($_REQUEST['increment_'.$i]))
        {
            $action = 'increment';
            $selected_part_id = isset($_REQUEST['id_'.$i]) ? (integer)$_REQUEST['id_'.$i] : 0;
        }
    }

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Suchresultate');

    try
    {
        $database           = new Database();
        $log                = new Log($database);
        $current_user       = new User($database, $current_user, $log, 1); // admin

        if ($selected_part_id > 0)
            $part = new Part($database, $current_user, $log, $selected_part_id);
        else
            $part = NULL;
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

        if(!$disable_pid_input)
        {
            //Check if keyword is a pid from a barcode scanner or so
            //This is the case if the input only contains digits and is 8 or 9 chars long
            if(is_numeric($keyword) && (mb_strlen($keyword) == 7 || mb_strlen($keyword) == 8))
            {
                if(mb_strlen($keyword) == 8)
                {
                    //Remove parity
                    $keyword = substr($keyword, 0, -1);
                }
                $pid = (integer) $keyword;
                header("Location: show_part_info.php?pid=" . $pid);
            }
        }

        switch ($action)
        {
            case 'decrement': // remove one part
                try
                {
                    if ( ! is_object($part))
                        throw new Exception('Es wurde keine gültige Bauteil-ID übermittelt!');

                    $part->set_instock($part->get_instock() - 1);

                    $reload_site = true;
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'increment': // add one part
                try
                {
                    if ( ! is_object($part))
                        throw new Exception('Es wurde keine gültige Bauteil-ID übermittelt!');

                    $part->set_instock($part->get_instock() + 1);

                    $reload_site = true;
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'export':
                try
                {
                    $parts = Part::search_parts($database, $current_user, $log, $keyword, '',
                                    $search_name, $search_description, $search_comment,
                                    $search_footprint, $search_category, $search_storelocation,
                                    $search_supplier, $search_supplierpartnr, $search_manufacturer);

                    $export_string = export_parts($parts, 'searchparts', $export_format_id, true, 'search_parts');
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;
        }
    }

    if (isset($reload_site) && $reload_site)
    {
        // reload the site to avoid multiple actions by manual refreshing
        $header = 'Location: show_search_parts.php?keyword='.$keyword;
        if ($search_name)           {$header.= '&search_name=1';}
        if ($search_category)       {$header.= '&search_category=1';}
        if ($search_description)    {$header.= '&search_description=1';}
        if ($search_comment)        {$header.= '&search_comment=1';}
        if ($search_supplier)       {$header.= '&search_supplier=1';}
        if ($search_supplierpartnr) {$header.= '&search_supplierpartnr=1';}
        if ($search_storelocation)  {$header.= '&search_storelocation=1';}
        if ($search_footprint)      {$header.= '&search_footprint=1';}
        if ($search_manufacturer)   {$header.= '&search_manufacturer=1';}
        header($header);
    }

    /********************************************************************************
    *
    *   Generate Table
    *
    *********************************************************************************/

    if ( ! $fatal_error)
    {
        try
        {
            $category_parts = Part::search_parts($database, $current_user, $log, $keyword, 'categories',
                                    $search_name, $search_description, $search_comment,
                                    $search_footprint, $search_category, $search_storelocation,
                                    $search_supplier, $search_supplierpartnr, $search_manufacturer);

            $hits_count = count($category_parts, COUNT_RECURSIVE) - count($category_parts);

            $parts_table_loops = array();

            foreach($category_parts as $category_full_path => $parts)
            {
                $parts_table_loops[$category_full_path] = Part::build_template_table_array($parts, 'search_parts');
            }
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            $fatal_error = true;
        }
    }

    /********************************************************************************
    *
    *   Set the rest of the HTML variables
    *
    *********************************************************************************/

    $html->use_javascript(array('popup'));

    $html->set_variable('keyword',                  $keyword,                               'string');
    $html->set_variable('hits_count',               (isset($hits_count) ? $hits_count : 0), 'integer');
    $html->set_variable('search_name',              $search_name,                           'boolean');
    $html->set_variable('search_category',          $search_category,                       'boolean');
    $html->set_variable('search_description',       $search_description,                    'boolean');
    $html->set_variable('search_comment',           $search_comment,                        'boolean');
    $html->set_variable('search_supplier',          $search_supplier,                       'boolean');
    $html->set_variable('search_supplierpartnr',    $search_supplierpartnr,                 'boolean');
    $html->set_variable('search_storelocation',     $search_storelocation,                  'boolean');
    $html->set_variable('search_footprint',         $search_footprint,                      'boolean');
    $html->set_variable('search_manufacturer',      $search_manufacturer,                   'boolean');

    if ( ! $fatal_error)
    {
        // export formats
        $html->set_loop('export_formats', build_export_formats_loop('searchparts'));

        // global stuff
        $html->set_variable('disable_footprints',       $config['footprints']['disable'],       'boolean');
        $html->set_variable('disable_manufacturers',    $config['manufacturers']['disable'],    'boolean');
        $html->set_variable('disable_auto_datasheets',  $config['auto_datasheets']['disable'],  'boolean');

        $html->set_variable('use_modal_popup',          $config['popup']['modal'],              'boolean');
        $html->set_variable('popup_width',              $config['popup']['width'],              'integer');
        $html->set_variable('popup_height',             $config['popup']['height'],             'integer');
    }

    /********************************************************************************
    *
    *   Generate HTML Output
    *
    *********************************************************************************/

    $html->print_header($messages);

    if ( ! $fatal_error)
    {
        $html->print_template('search_header');

        foreach($parts_table_loops as $category_full_path => $loop)
        {
            $html->set_variable('category_full_path', $category_full_path, 'string');
            $html->set_variable('table_rowcount', count($loop), 'integer');
            $html->set_loop('table', $loop);
            $html->print_template('searched_parts_table');
        }
    }

    $html->print_footer();
