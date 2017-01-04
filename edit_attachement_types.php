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
        2012-09-12  kami89              - created (copied from edit_filetypes.php)
*/

    /*
     * Please note:
     *  The files "edit_categories.php", "edit_footprints.php", "edit_manufacturers.php",
     *  "edit_suppliers.php", "edit_devices.php", "edit_storelocations.php" and "edit_filetypes.php"
     *  are quite similar.
     *  If you make changes in one of them, please check if you should change the other files too.
     */

    include_once('start_session.php');

    class EditAttachmentTypesPage extends EditPage {


        protected $root_attachement_type;

        protected $selected_id, $new_name, $new_parent_id, $new_attachement_type;
        protected $selected_attachement_type;

        protected function init_objects()
        {
            $this->root_attachement_type  = new AttachementType($this->database, $this->current_user, $this->log, 0);
        }

        protected function evaluate_requests()
        {
            $this->selected_id                = isset($_REQUEST['selected_id'])   ? (integer)$_REQUEST['selected_id'] : 0;
            $this->new_name                   = isset($_REQUEST['name'])          ? (string)$_REQUEST['name']         : '';
            $this->new_parent_id              = isset($_REQUEST['parent_id'])     ? (integer)$_REQUEST['parent_id']   : 0;
            $this->add_more                   = isset($_REQUEST['add_more']);

            if ($this->selected_id > 0)
                $this->selected_attachement_type = new AttachementType($this->database, $this->current_user, $this->log, $this->selected_id);
            else
                $this->selected_attachement_type = NULL;
        }

        protected function action_add($html)
        {
            $this->new_attachement_type = AttachementType::add($this->database, $this->current_user, $this->log, $this->new_name, $this->new_parent_id);

            if ( ! $this->add_more)
            {
                $this->selected_attachement_type = $this->new_attachement_type;
                $this->selected_id = $this->selected_attachement_type->get_id();
            }
        }

        protected function action_delete($html)
        {
            if ( ! is_object($this->selected_attachement_type))
                throw new Exception(_('Es ist kein Dateityp markiert oder es trat ein Fehler auf!'));

            $attachements = $this->selected_attachement_type->get_attachements();
            $count = count($attachements);

            if ($count > 0)
            {
                $this->add_message(array('text' => sprintf(_('Es gibt noch %d Dateianhänge mit diesem Dateityp, '.
                    'daher kann der Dateityp nicht gelöscht werden.'), $count), 'strong' => true, 'color' => 'red'));
            }
            else
            {
                $notes[] = _("Es gibt keine Dateianhänge mit diesem Dateityp.");
                $notes[] = _("Beinhaltet diese Dateityp noch Unterdateitypen, dann werden diese eine Ebene nach oben verschoben.");
                $title = sprintf(_("Soll der Dateityp %s wirklich unwiederruflich gelöscht werden?"), $this->selected_attachement_type->get_full_path());

                $dialog = generate_delete_dialog($this->selected_attachement_type->get_id(), $title, $notes, _("Ja, Dateityp löschen"), _("Nein, nicht löschen"));

                $this->add_message($dialog);
            }
        }

        protected function action_delete_confirmed($html)
        {
            if ( ! is_object($this->selected_attachement_type))
                throw new Exception(_('Es ist kein Dateityp markiert oder es trat ein Fehler auf!'));

            $this->selected_attachement_type->delete();
            $this->selected_attachement_type = NULL;
        }

        protected function action_apply($html)
        {
            if ( ! is_object($this->selected_attachement_type))
                throw new Exception(_('Es ist kein Dateityp markiert oder es trat ein Fehler auf!'));

            $this->selected_attachement_type->set_attributes(array(   'name'      => $this->new_name,
                'parent_id' => $this->new_parent_id));
        }

        protected function action_shared($html)
        {
            $html->set_variable('add_more', $this->add_more, 'boolean');

            if (is_object($this->selected_attachement_type))
            {
                $parent_id = $this->selected_attachement_type->get_parent_id();
                $html->set_variable('id', $this->selected_attachement_type->get_id(), 'integer');
                $name = $this->selected_attachement_type->get_name();
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

            $attachement_types_list = $this->root_attachement_type->build_html_tree($this->selected_id, true, false);
            $html->set_variable('attachement_types_list', $attachement_types_list, 'string');

            $parent_attachement_types_list = $this->root_attachement_type->build_html_tree($parent_id, true, true);
            $html->set_variable('parent_attachement_types_list', $parent_attachement_types_list, 'string');
        }

        protected function print_templates($html)
        {
            $html->print_template('edit_attachement_types');
        }

        protected function generate_reload_link()
        {
            return 'edit_attachement_types.php';
        }
    }


    $page = new EditAttachmentTypesPage(_("Dateitypen"));
    $page->run();
