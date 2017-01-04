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
        [DATE]      [NICKNAME]      [CHANGES]
        2012-09-08  kami89          - created (copied from edit_suppliers.php)
        2014-05-12  kami89          - added attribute "auto_product_url"
*/

    /*
     * Please note:
     *  The files "edit_categories.php", "edit_footprints.php", "edit_manufacturers.php",
     *  "edit_suppliers.php", "edit_devices.php", "edit_storelocations.php" and "edit_filetypes.php"
     *  are quite similar.
     *  If you make changes in one of them, please check if you should change the other files too.
     */

    include_once('start_session.php');

    class EditManufacturersPage extends EditPage
    {
        protected $selected_id, $new_name, $new_parent_id, $new_address, $new_phone_number, $new_fax_number;
        protected $new_website, $new_auto_product_url;

        protected $root_manufacturer;
        protected $new_manufacturer;

        protected function evaluate_requests()
        {
            $this->selected_id          = isset($_REQUEST['selected_id'])      ? (integer)$_REQUEST['selected_id']     : 0;
            $this->new_name             = isset($_REQUEST['name'])             ? (string)$_REQUEST['name']             : '';
            $this->new_parent_id        = isset($_REQUEST['parent_id'])        ? (integer)$_REQUEST['parent_id']       : 0;
            $this->new_address          = isset($_REQUEST['address'])          ? (string)$_REQUEST['address']          : '';
            $this->new_phone_number     = isset($_REQUEST['phone_number'])     ? (string)$_REQUEST['phone_number']     : '';
            $this->new_fax_number       = isset($_REQUEST['fax_number'])       ? (string)$_REQUEST['fax_number']       : '';
            $this->new_email_address    = isset($_REQUEST['email_address'])    ? (string)$_REQUEST['email_address']    : '';
            $this->new_website          = isset($_REQUEST['website'])          ? (string)$_REQUEST['website']          : '';
            $this->new_auto_product_url = isset($_REQUEST['auto_product_url']) ? (string)$_REQUEST['auto_product_url'] : '';
            $this->add_more             = isset($_REQUEST['add_more']);
        }

        protected function init_objects()
        {
            $this->root_manufacturer  = new Manufacturer($this->database, $this->current_user, $this->log, 0);

            if ($this->selected_id > 0)
                $this->selected_manufacturer = new Manufacturer($this->database, $this->current_user, $this->log, $this->selected_id);
            else
                $this->selected_manufacturer = NULL;
        }

        protected function action_add($html)
        {
            $this->new_manufacturer = Manufacturer::add(  $this->database, $this->current_user, $this->log, $this->new_name,
                $this->new_parent_id, $this->new_address, $this->new_phone_number,
                $this->new_fax_number, $this->new_email_address, $this->new_website,
                $this->new_auto_product_url);

            if ( ! $this->add_more)
            {
                $this->selected_manufacturer = $this->new_manufacturer;
                $this->selected_id = $this->selected_manufacturer->get_id();
            }
        }

        protected function action_delete($html)
        {
            if ( ! is_object($this->selected_manufacturer))
                throw new Exception(_('Es ist kein Hersteller markiert oder es trat ein Fehler auf!'));

            $parts = $this->selected_manufacturer->get_parts();
            $count = count($parts);

            if ($count > 0)
            {
                $this->add_message( array('text' => sprintf(_('Es gibt noch %d Bauteile mit diesem Hersteller, '.
                    'daher kann der Hersteller nicht gelöscht werden.'), $count), 'strong' => true, 'color' => 'red'));
            }
            else
            {
                $notes[] = _("Es gibt keine Bauteile, die diesen Hersteller zugeordnet haben.");
                $notes[] = _("Beinhaltet dieser Hersteller noch Unterhersteller, dann werden diese eine Ebene nach oben verschoben.");
                $title = sprintf(_('Soll der Hersteller "%s'.
                    '" wirklich unwiederruflich gelöscht werden?'),$this->selected_manufacturer->get_full_path());

                $dialog = generate_delete_dialog($this->selected_manufacturer->get_id(), $title, $notes, _('Ja, Hersteller löschen'), _('Nein, nicht löschen') );

                $this->add_message($dialog);
            }
        }

        protected function action_delete_confirmed($html)
        {
            if ( ! is_object($this->selected_manufacturer))
                throw new Exception(_('Es ist kein Hersteller markiert oder es trat ein Fehler auf!'));

            $this->selected_manufacturer->delete();
            $this->selected_manufacturer = NULL;
        }

        protected function action_apply($html)
        {
            if ( ! is_object($this->selected_manufacturer))
                        throw new Exception(_('Es ist kein Hersteller markiert oder es trat ein Fehler auf!'));

            $this->selected_manufacturer->set_attributes(array(   'name'             => $this->new_name,
                        'parent_id'        => $this->new_parent_id,
                        'address'          => $this->new_address,
                        'phone_number'     => $this->new_phone_number,
                        'fax_number'       => $this->new_fax_number,
                        'email_address'    => $this->new_email_address,
                        'website'          => $this->new_website,
                        'auto_product_url' => $this->new_auto_product_url));
        }

        protected function action_shared($html)
        {
            if (is_object($this->selected_manufacturer))
            {
                $parent_id = $this->selected_manufacturer->get_parent_id();
                $html->set_variable('id', $this->selected_manufacturer->get_id(), 'integer');
                $html->set_variable('name', $this->selected_manufacturer->get_name(), 'string');
                $html->set_variable('address', $this->selected_manufacturer->get_address(), 'string');
                $html->set_variable('phone_number', $this->selected_manufacturer->get_phone_number(), 'string');
                $html->set_variable('fax_number', $this->selected_manufacturer->get_fax_number(), 'string');
                $html->set_variable('email_address', $this->selected_manufacturer->get_email_address(), 'string');
                $html->set_variable('website', $this->selected_manufacturer->get_website(), 'string');
                $html->set_variable('auto_product_url', $this->selected_manufacturer->get_auto_product_url(NULL), 'string');
            }
            elseif ($this->action == 'add')
            {
                $parent_id = $this->new_parent_id;
            }
            else
            {
                $parent_id = 0;
            }

            $manufacturer_list = $this->root_manufacturer->build_html_tree($this->selected_id, true, false);
            $html->set_variable('manufacturer_list', $manufacturer_list, 'string');

            $parent_manufacturer_list = $this->root_manufacturer->build_html_tree($parent_id, true, true);
            $html->set_variable('parent_manufacturer_list', $parent_manufacturer_list, 'string');
        }


        protected function print_templates($html)
        {
            $html->print_template('edit_manufacturers');
        }

        protected function generate_reload_link()
        {
            return "edit_manufacturers.php";
        }
    }

    $page = new EditManufacturersPage();
    $page->run();