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

    class EditStorelocationsPage extends EditPage {

        protected $selected_id, $new_name, $new_parent_id, $new_is_full, $create_series, $series_from, $series_to, $add_more;

        protected $root_storelocation;
        protected $selected_storelocation;

        protected function evaluate_requests()
        {
            $this->selected_id        = isset($_REQUEST['selected_id'])   ? (integer)$_REQUEST['selected_id'] : 0;
            $this->new_name           = isset($_REQUEST['name'])          ? (string)$_REQUEST['name']         : '';
            $this->new_parent_id      = isset($_REQUEST['parent_id'])     ? (integer)$_REQUEST['parent_id']   : 0;
            $this->new_is_full        = isset($_REQUEST['is_full']);
            $this->create_series      = isset($_REQUEST['series']);
            $this->series_from        = isset($_REQUEST['series_from'])   ? $_REQUEST['series_from'] : 1;
            $this->series_to          = isset($_REQUEST['series_to'])     ? $_REQUEST['series_to']   : 1;
            $this->add_more           = isset($_REQUEST['add_more']);
        }

        protected function init_objects()
        {
            $this->root_storelocation = new Storelocation($this->database, $this->current_user, $this->log, 0);

            if ($this->selected_id > 0)
                $this->selected_storelocation = new Storelocation($this->database, $this->current_user, $this->log, $this->selected_id);
            else
                $this->selected_storelocation = NULL;
        }



        protected function action_add($html)
        {
            if ($this->create_series)
            {
                $width  = mb_strlen((string) $this->series_to); // determine the width of second argument
                $format = "%0". (int)$width ."s";

                foreach (range($this->series_from, $this->series_to) as $index)
                {
                    $new_storelocation_name = $this->new_name . sprintf($format, $index);
                    $new_storelocation = Storelocation::add(    $this->database, $this->current_user, $this->log,
                        $new_storelocation_name,
                        $this->new_parent_id, $this->new_is_full);
                }
            }
            else
            {
                $new_storelocation = Storelocation::add(  $this->database, $this->current_user, $this->log, $this->new_name,
                    $this->new_parent_id, $this->new_is_full);
            }

            if ( ! $this->add_more)
            {
                $this->selected_storelocation = new_storelocation;
                $this->selected_id = $this->selected_storelocation->get_id();
            }
        }

        protected function action_delete($html)
        {
            $parts = $this->selected_storelocation->get_parts();
            $count = count($parts);

            if ($count > 0)
            {
                $messages[] = array('text' => sprintf(_('Es gibt noch %d Bauteile an diesem Lagerort, '.
                    'daher kann der Lagerort nicht gelöscht werden.'),$count), 'strong' => true, 'color' => 'red');
            }
            else
            {
                $title = sprintf(_('Soll der Lagerort "%s" wirklich unwiederruflich gelöscht werden?'), $this->selected_storelocation->get_full_path());
                $notes[] = _("Es gibt keine Bauteile an diesem Lagerort.");
                $notes[] = _("Beinhaltet dieser Lagerort noch Unterlagerorte, dann werden diese eine Ebene nach oben verschoben.");
                $dialog = generate_delete_dialog($this->selected_storelocation->get_id(), $title, $notes, _('Ja, Lagerort löschen'), _('Nein, nicht löschen'));
                $this->add_message($dialog);
            }
        }

        protected function action_delete_confirmed($html)
        {
            if (is_object($this->selected_storelocation))
            {
                try
                {
                    $this->selected_storelocation->delete();
                    $this->selected_storelocation = NULL;
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => _('Der Lagerort konnte nicht gelöscht werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                    $this->add_message($messages);
                }
            }
            else
            {
                $messages[] = array('text' => _('Es ist kein Lagerort markiert oder es trat ein Fehler auf!'),
                    'strong' => true, 'color' => 'red');
                $this->add_message($messages);
            }
        }

        protected function action_apply($html)
        {
            if (is_object($this->selected_storelocation))
            {
                    try
                    {
                        $this->selected_storelocation->set_attributes(array(  'name'       => $this->new_name,
                            'parent_id'  => $this->new_parent_id,
                            'is_full'    => $this->new_is_full));
                    }
                    catch (Exception $e)
                    {
                        $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                        $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                        $this->add_message($messages);
                    }
            }
            else
            {
                    $messages[] = array('text' => _('Es ist kein Lagerort markiert oder es trat ein Fehler auf!'),
                        'strong' => true, 'color' => 'red');
                    $this->add_message($messages);
            }
        }

        protected function action_shared($html)
        {
            if (is_object($this->selected_storelocation))
            {
                $parent_id = $this->selected_storelocation->get_parent_id();
                $html->set_variable('id', $this->selected_storelocation->get_id(), 'integer');
                $name = $this->selected_storelocation->get_name();
                $is_full = $this->selected_storelocation->get_is_full();
            }
            elseif ($this->action == 'add')
            {
                $parent_id = $this->new_parent_id;
                $name = $this->new_name;
                $is_full = $this->new_is_full;
            }
            else
            {
                $parent_id = 0;
                $name = '';
                $is_full = false;
            }

            $html->set_variable('name', $name, 'string');
            $html->set_variable('is_full', $is_full, 'boolean');

            $storelocation_list = $this->root_storelocation->build_html_tree($this->selected_id, true, false);
            $html->set_variable('storelocation_list', $storelocation_list, 'string');

            $parent_storelocation_list = $this->root_storelocation->build_html_tree($parent_id, true, true);
            $html->set_variable('parent_storelocation_list', $parent_storelocation_list, 'string');
        }

        protected function print_templates($html)
        {
            $html->print_template('edit_storelocations');
        }

        protected function generate_reload_link()
        {
            return "edit_storelocations.php";
        }
    }

    $page = new EditStorelocationsPage(_("Lagerorte"));
    $page->run();