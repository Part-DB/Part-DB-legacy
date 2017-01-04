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
        2012-09-10  kami89              - changed to OOP
*/

    /*
     * Please note:
     *  The files "edit_categories.php", "edit_footprints.php", "edit_manufacturers.php",
     *  "edit_suppliers.php", "edit_devices.php", "edit_storelocations.php" and "edit_filetypes.php"
     *  are quite similar.
     *  If you make changes in one of them, please check if you should change the other files too.
     */

    include_once('start_session.php');


    class EditDevicePage extends EditPage
    {

        protected $selected_id, $new_name, $new_parent_id;

        protected $root_device;
        protected $selected_device;

        protected function evaluate_requests()
        {
        }

        protected function init_objects()
        {
            $this->root_device        = new Device($this->database, $this->current_user, $this->log, 0);

            if ($this->selected_id > 0)
                $this->selected_device = new Device($this->database, $this->current_user, $this->log, $this->selected_id);
            else
                $this->selected_device = NULL;
        }



        protected function action_add($html)
        {
            $new_device = Device::add(  $this->database, $this->current_user, $this->log, $this->new_name, $this->new_parent_id);

            $html->set_variable('refresh_navigation_frame', true, 'boolean');

            if ( ! $this->add_more)
            {
                $this->selected_device = $new_device;
                $this->selected_id = $this->selected_device->get_id();
            }
        }

        protected function action_delete($html)
        {
            if ( ! is_object($this->selected_device))
                throw new Exception(_('Es ist keine Baugruppe markiert oder es trat ein Fehler auf!'));

            $parts = $this->selected_device->get_parts();
            $count = count($parts);

            if ($count > 0)
                $notes[] = sprintf(_('Es gibt noch %d Bauteile in dieser Baugruppe!'), $count);
            else
                $notes[] = _('Es gibt keine Bauteile in dieser Baugruppe.');
            $notes[] = _('Beinhaltet diese Baugruppe noch Unterbaugruppen, dann werden diese eine Ebene nach oben verschoben.');

            $title = sprintf(_('Soll die Baugruppe "%s" wirklich unwiederruflich gelöscht werden?'), $this->selected_device->get_full_path());

            $dialog = generate_delete_dialog($this->selected_device->get_id(), $title, $notes, _('Ja, Baugruppe löschen'), _("Nein, nicht löschen"));
            $this->add_message($dialog);

        }

        protected function action_delete_confirmed($html)
        {
            if ( ! is_object($this->selected_device))
                throw new Exception(_('Es ist keine Baugruppe markiert oder es trat ein Fehler auf!'));

            $this->selected_device->delete();
            $this->selected_device = NULL;

            $html->set_variable('refresh_navigation_frame', true, 'boolean');
        }

        protected function action_apply($html)
        {
            if ( ! is_object($this->selected_device))
                throw new Exception(_('Es ist keine Baugruppe markiert oder es trat ein Fehler auf!'));

            $this->selected_device->set_attributes(array( 'name' => $this->new_name,
                'parent_id'             => $this->new_parent_id));

            $html->set_variable('refresh_navigation_frame', true, 'boolean');
        }

        protected function action_shared($html)
        {
            if (is_object($this->selected_device))
            {
                $parent_id = $this->selected_device->get_parent_id();
                $html->set_variable('id', $this->selected_device->get_id(), 'integer');
                $name = $this->selected_device->get_name();
            }
            elseif ($this->action == 'add')
            {
                $parent_id = $this->new_parent_id;
                $name = $this->new_name;
            }
            else
            {
                $parent_id = 0;
                $name = '';
            }

            $html->set_variable('name', $name, 'string');

            $device_list = $this->root_device->build_html_tree($this->selected_id, true, false);
            $html->set_variable('device_list', $device_list, 'string');

            $parent_device_list = $this->root_device->build_html_tree($parent_id, true, true);
            $html->set_variable('parent_device_list', $parent_device_list, 'string');
        }

        protected function print_templates($html)
        {
            $html->print_template('edit_devices');
        }

        protected function generate_reload_link()
        {
            return "edit_devices.php";
        }
    }

    $page = new EditDevicePage(_("Baugruppen"));
    $page->run();