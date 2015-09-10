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
*/

    include_once('start_session.php');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Statistik');

    try
    {
        $database           = new Database();
        $log                = new Log($database);
        $current_user       = new User($database, $current_user, $log, 1); // admin
    }
    catch (Exception $e)
    {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }

    /********************************************************************************
    *
    *   Set all HTML variables
    *
    *********************************************************************************/

    if (! $fatal_error)
    {
        try
        {
            $noprice_parts              = Part::get_noprice_parts($database, $current_user, $log);
            $count_of_parts_with_price  = Part::get_count($database) - count($noprice_parts); // :-)

            $html->set_variable('parts_count_with_prices',  $count_of_parts_with_price,             'integer');
            $html->set_variable('parts_count_sum_value',    Part::get_sum_price_instock($database, $current_user, $log, true), 'string');

            $html->set_variable('parts_count',              Part::get_count($database),             'integer');
            $html->set_variable('parts_count_sum_instock',  Part::get_sum_count_instock($database), 'integer');

            $html->set_variable('categories_count',         Category::get_count($database),         'integer');
            $html->set_variable('footprint_count',          Footprint::get_count($database),        'integer');
            $html->set_variable('location_count',           Storelocation::get_count($database),    'integer');
            $html->set_variable('suppliers_count',          Supplier::get_count($database),         'integer');
            $html->set_variable('manufacturers_count',      Manufacturer::get_count($database),     'integer');
            $html->set_variable('devices_count',            Device::get_count($database),           'integer');
            $html->set_variable('attachements_count',       Attachement::get_count($database),      'integer');

            $html->set_variable('footprint_picture_count',  count(find_all_files(BASE.'/img/footprints/',   true)), 'integer');
            $html->set_variable('iclogos_picture_count',    count(find_all_files(BASE.'/img/iclogos/',      true)), 'integer');
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

    $html->print_header($messages);

    if (! $fatal_error)
        $html->print_template('statistics');

    $html->print_footer();

?>
