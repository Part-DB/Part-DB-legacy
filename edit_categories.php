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


    class EditCategoriesPage extends EditPage {

        protected $root_category;
        protected $selected_category;
        protected $selected_id, $new_name, $new_parent_id, $new_disable_footprints, $new_disable_auto_datasheets;

        protected function init_objects()
        {
            $this->root_category      = new Category($this->database, $this->current_user, $this->log, 0);

            if ($this->selected_id > 0)
                $this->selected_category = new Category($this->database, $this->current_user, $this->log, $this->selected_id);
            else
                $this->selected_category = NULL;
        }

        protected function evaluate_requests()
        {
            $this->selected_id                = isset($_REQUEST['selected_id'])   ? (integer)$_REQUEST['selected_id'] : 0;
            $this->new_name                   = isset($_REQUEST['name'])          ? (string)$_REQUEST['name']         : '';
            $this->new_parent_id              = isset($_REQUEST['parent_id'])     ? (integer)$_REQUEST['parent_id']   : 0;
            $this->new_disable_footprints     = isset($_REQUEST['disable_footprints']);
            $this->new_disable_manufacturers  = isset($_REQUEST['disable_manufacturers']);
            $this->new_disable_autodatasheets = isset($_REQUEST['disable_autodatasheets']);
            $this->add_more                   = isset($_REQUEST['add_more']);
        }

        protected function print_templates($html)
        {
            $html->print_template('edit_categories');
        }

        protected function generate_reload_link()
        {
            return "edit_categories.php";
        }

        protected function action_add($html)
        {
            $this->new_category = Category::add(  $this->database, $this->current_user, $this->log, $this->new_name,
                $this->new_parent_id, $this->new_disable_footprints,
                $this->new_disable_manufacturers, $this->new_disable_autodatasheets);

            $html->set_variable('refresh_navigation_frame', true, 'boolean');

            if ( ! $this->add_more)
            {
                $this->selected_category = $this->new_category;
                $this->selected_id = $this->selected_category->get_id();
            }
        }

        protected function action_delete($html)
        {
            if ( ! is_object($this->selected_category))
                throw new Exception(_('Es ist keine Kategorie markiert oder es trat ein Fehler auf!'));

            $parts = $this->selected_category->get_parts();
            $count = count($parts);

            if ($count > 0)
            {
                 $this->add_message(array('text' => sprintf(_('Es gibt noch %d Bauteile in dieser Kategorie, '.
                    'daher kann die Kategorie nicht gelöscht werden.'), $count), 'strong' => true, 'color' => 'red'));
            }
            else {
                $notes[] = _('Es gibt keine Bauteile in dieser Kategorie.');
                $notes[] = _('Beinhaltet diese Kategorie noch Unterkategorien, dann werden diese eine Ebene nach oben verschoben.');
                $title = sprintf(_('Soll die Kategorie "%s' .
                    '" wirklich unwiederruflich gelöscht werden?'), $this->selected_category->get_full_path());
                $dialog = generate_delete_dialog($this->selected_category->get_id(), $title, $notes, _('Ja, Kategorie löschen'), _('Nein, nicht löschen'));
                $messages = array_merge($this->messages, $dialog);

                $this->add_message($messages);
            }
        }

        protected function action_delete_confirmed($html)
        {
            if ( ! is_object($this->selected_category))
                throw new Exception(_('Es ist keine Kategorie markiert oder es trat ein Fehler auf!'));

            $this->selected_category->delete();
            $this->selected_category = NULL;

            $html->set_variable('refresh_navigation_frame', true, 'boolean');
        }

        protected function action_apply($html)
        {
            if ( ! is_object($this->selected_category))
                throw new Exception(_('Es ist keine Kategorie markiert oder es trat ein Fehler auf!'));

            $this->selected_category->set_attributes(array('name'                     => $this->new_name,
                'parent_id'                => $this->new_parent_id,
                'disable_footprints'       => $this->new_disable_footprints,
                'disable_manufacturers'    => $this->new_disable_manufacturers,
                'disable_autodatasheets'   => $this->new_disable_autodatasheets));

            $html->set_variable('refresh_navigation_frame', true, 'boolean');
        }

        protected function action_shared($html)
        {
            $html->set_variable('add_more', $this->add_more, 'boolean');

            if (is_object($this->selected_category))
            {
                $parent_id = $this->selected_category->get_parent_id();
                $html->set_variable('id', $this->selected_category->get_id(), 'integer');
                $name = $this->selected_category->get_name();

                $disable_footprints = $this->selected_category->get_disable_footprints(true);
                $disable_manufacturers = $this->selected_category->get_disable_manufacturers(true);
                $disable_autodatasheets = $this->selected_category->get_disable_autodatasheets(true);

                $html->set_variable('parent_disable_footprints', ($this->selected_category->get_disable_footprints(true)
                    && ( ! $this->selected_category->get_disable_footprints(false))), 'boolean');
                $html->set_variable('parent_disable_manufacturers', ($this->selected_category->get_disable_manufacturers(true)
                    && ( ! $this->selected_category->get_disable_manufacturers(false))), 'boolean');
                $html->set_variable('parent_disable_autodatasheets', ($this->selected_category->get_disable_autodatasheets(true)
                    && ( ! $this->selected_category->get_disable_autodatasheets(false))), 'boolean');
            }
            elseif ($this->action == 'add')
            {
                $parent_id = $this->new_parent_id;
                $name = $this->new_name;
                $disable_footprints = $this->new_disable_footprints;
                $disable_manufacturers = $this->new_disable_manufacturers;
                $disable_autodatasheets = $this->new_disable_autodatasheets;
            }
            else
            {
                $parent_id = 0;
                $name = '';
                $disable_footprints = false;
                $disable_manufacturers = false;
                $disable_autodatasheets = false;
            }

            $html->set_variable('name', $name, 'string');
            $html->set_variable('disable_footprints', $disable_footprints, 'boolean');
            $html->set_variable('disable_manufacturers', $disable_manufacturers, 'boolean');
            $html->set_variable('disable_autodatasheets', $disable_autodatasheets, 'boolean');

            $category_list = $this->root_category->build_html_tree($this->selected_id, true, false);
            $html->set_variable('category_list', $category_list, 'string');

            $parent_category_list = $this->root_category->build_html_tree($parent_id, true, true);
            $html->set_variable('parent_category_list', $parent_category_list, 'string');
        }
    }

    $page = new EditCategoriesPage(_("Kategorien"));
    $page->run();