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
        2012-08-??  kami89          - created
        2012-09-28  kami89          - added doxygen comments
        2013-01-29  kami89          - added support for transactions in "delete()"
        2013-07-14  kami89          - added function "is_child_of()"
*/

     /**
     * @file class.StructuralDBElement.php
     * @brief class StructuralDBElement
     *
     * This class is for managing all database objects with a structural design.
     * All these sub-objects must have the table columns 'id', 'name' and 'parent_id' (at least)!
     * The root node has always the ID '0'.
     * It's allowed to have instances of root elements, but if you try to change
     * an attribute of a root element, you will get an exception!
     *
     * @class StructuralDBElement
     * @brief All elements with the fields "id", "name" and "parent_id" (at least)
     * @author kami89
     */
    abstract class StructuralDBElement extends AttachementsContainingDBElement
    {
        /********************************************************************************
        *
        *   Calculated Attributes
        *
        *   Calculated attributes will be NULL until they are requested for first time (to save CPU time)!
        *   After changing an element attribute, all calculated data will be NULLed again.
        *   So: the calculated data will be cached.
        *
        *********************************************************************************/

        /** @brief (array) all names of all parent elements as a array of strings,
         *  the last array element is the name of the element itself */
        private $full_path_strings =  NULL;

        /** @brief (integer) the level of the most top elements is zero */
        private $level =              NULL;

        /** @brief (array) all subelements (not recursive) of this element as a array of objects */
        private $subelements =        NULL;

        /********************************************************************************
        *
        *   Constructor / Destructor / reset_attributes()
        *
        *********************************************************************************/

        /**
         * @brief Constructor
         *
         * It's allowed to create an object with the ID 0 (for the root element).
         *
         * @param Database  &$database      reference to the Database-object
         * @param User      &$current_user  reference to the current user which is logged in
         * @param Log       &$log           reference to the Log-object
         * @param string    $tablename      name of the table where the elements are located
         * @param integer   $id             ID of the element we want to get
         *
         * @throws Exception        if there is no such element in the database
         * @throws Exception        if there was an error
         */
        public function __construct(&$database, &$current_user, &$log, $tablename, $id)
        {
            parent::__construct($database, $current_user, $log, $tablename, $id, true);

            if ($id == 0)
            {
                // this is the root node
                $this->db_data['id'] = NULL;
                $this->db_data['name'] = 'Oberste Ebene';
                $this->db_data['parent_id'] = -1;
                $this->full_path_strings = array('Oberste Ebene');
                $this->level = -1;
            }
        }

        /**
         * @copydoc DBElement::reset_attributes()
         */
        public function reset_attributes($all = false)
        {
            $this->full_path_strings    = NULL;
            $this->level                = NULL;
            $this->subelements          = NULL;

            parent::reset_attributes($all);
        }

        /********************************************************************************
        *
        *   Basic Methods
        *
        *********************************************************************************/

        /**
         * @brief Delete this element
         *
         * @note    This function overrides the same-named method from the parent class.
         *          (Because of the argument $delete_recursive, we need to redefine this method.)
         *
         * @param boolean $delete_recursive             @li if true, all child elements (recursive)
         *                                                  will be deleted too (!!)
         *                                              @li if false, the parent of the child nodes (not recursive)
         *                                                  will be changed to the parent element of this element
         * @param boolean $delete_files_from_hdd        if true, all attached files from this element will be deleted
         *                                                  from harddisc drive (!!)
         *
         * @throws Exception if there was an error
         */
        public function delete($delete_recursive = false, $delete_files_from_hdd = false)
        {
            if ($this->get_id() == NULL)
                throw new Exception('Die Oberste Ebene kann nicht gelöscht werden!');

            try
            {
                $transaction_id = $this->database->begin_transaction(); // start transaction

                // first, we take all subelements of this element...
                $subelements = $this->get_subelements(false);

                // then we set $this->subelements to NULL, because if there was an error while deleting
                $this->reset_attributes();

                // ant then we change the parent IDs of the subelments to the parent ID of this element
                foreach ($subelements as $element)
                {
                    if ($delete_recursive)
                        $element->delete(true, $delete_files_from_hdd); // delete it with all child nodes (!!)
                    else
                        $element->set_parent_id($this->get_parent_id()); // just change its parent
                }

                // now we can delete this element + all attachements of it
                parent::delete($delete_files_from_hdd);

                $this->database->commit($transaction_id); // commit transaction
            }
            catch (Exception $e)
            {
                $this->database->rollback(); // rollback transaction

                // restore the settings from BEFORE the transaction
                $this->reset_attributes();

                throw new Exception("Das Element \"".$this->get_name()."\" konnte nicht gelöscht werden!\nGrund: ".$e->getMessage());
            }
        }

        /**
         * @brief Check if this element is a child of another element (recursive)
         *
         * @param object $another_element       the object to compare
         *                                      IMPORTANT: both objects to compare must be from the same class (for example two "Device" objects)!
         *
         * @retval true|false
         *
         * @throws Exception if there was an error
         */
        public function is_child_of($another_element)
        {
            if ($this->get_id() == NULL) // this is the root node
            {
                return false;
            }
            else
            {
                $class_name = get_class($this);
                $parent_element = new $class_name($this->database, $this->current_user, $this->log, $this->get_parent_id());

                return (($parent_element->get_id() == $another_element->get_id()) || ($parent_element->is_child_of($another_element)));
            }
        }

        /********************************************************************************
        *
        *   Getters
        *
        *********************************************************************************/

        /**
         * @brief Get the parent-ID
         *
         * @retval integer|NULL     @li the ID of the parent element
         *                          @li NULL means, the parent is the root node
         *                          @li the parent ID of the root node is -1
         */
        public function get_parent_id()
        {
            return $this->db_data['parent_id'];
        }

        /**
         * @brief Get the level
         *
         * @note    The level of the root node is -1.
         *
         * @retval integer      the level of this element (zero means a most top element
         *                      [a subelement of the root node])
         *
         * @throws Exception if there was an error
         */
        public function get_level()
        {
            if ($this->level === NULL)
            {
                $this->level = 0;
                $parent_id = $this->get_parent_id();
                $class = get_class($this);
                while ($parent_id > 0)
                {
                    $element = new $class($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $element->get_parent_id();
                    $this->level++;
                }
            }

            return $this->level;
        }

        /**
         * @brief Get the full path
         *
         * @param string $delimeter     the delimeter of the returned string
         *
         * @retval string       the full path (incl. the name of this element), delimeted by $delimeter
         *
         * @throws Exception    if there was an error
         */
        public function get_full_path($delimeter = ' → ')
        {
            if ( ! is_array($this->full_path_strings))
            {
                $this->full_path_strings = array();
                $this->full_path_strings[] = $this->get_name();
                $parent_id = $this->get_parent_id();
                $class = get_class($this);
                while ($parent_id > 0)
                {
                    $element = new $class($this->database, $this->current_user, $this->log, $parent_id);
                    $parent_id = $element->get_parent_id();
                    $this->full_path_strings[] = $element->get_name();
                }
                $this->full_path_strings = array_reverse($this->full_path_strings);
            }

            return implode($delimeter, $this->full_path_strings);
        }

        /**
         * @brief Get all subelements of this element
         *
         * @param boolean $recursive        if true, the search is recursive
         *
         * @retval array        all subelements as an array of objects (sorted by their full path)
         *
         * @throws Exception    if there was an error
         */
        public function get_subelements($recursive)
        {
            if ( ! is_array($this->subelements))
            {
                $this->subelements = array();

                $query_data = $this->database->query('SELECT id FROM '. $this->tablename .
                                                     ' WHERE parent_id <=> ? ORDER BY name ASC', array($this->get_id()));

                $class = get_class($this);
                foreach ($query_data as $row)
                    $this->subelements[] = new $class($this->database, $this->current_user, $this->log, $row['id']);
            }

            if ( ! $recursive)
            {
                return $this->subelements;
            }
            else
            {
                $all_elements = array();
                foreach ($this->subelements as $subelement)
                {
                    $all_elements[] = $subelement;
                    $all_elements = array_merge($all_elements, $subelement->get_subelements(true));
                }

                return $all_elements;
            }
        }

        /********************************************************************************
        *
        *   Setters
        *
        *********************************************************************************/

        /**
         * @brief Change the parent ID of this element
         *
         * @param integer|NULL $new_parent_id           @li the ID of the new parent element
         *                                              @li NULL if the parent should be the root node
         *
         * @throws Exception if the new parent ID is not valid
         * @throws Exception if there was an error
         */
        public function set_parent_id($new_parent_id)
        {
            $this->set_attributes(array('parent_id' => $new_parent_id));
        }

        /********************************************************************************
        *
        *   Tree / Table Builders
        *
        *********************************************************************************/

        /**
         * @brief Build a JavaScript tree with all subcategories of this element
         *
         * @param string    $tree_name     name of the tree (like 'cat_navtree', this is not visible for the user)
         * @param string    $page          filename of the target page of the nodes (e.g. 'showparts.php')
         * @param string    $parameter     name of the parameter (e.g. 'cid' for 'showparts.php?cid=')
         * @param string    $target        target frame (e.g. 'content_frame')
         * @param boolean   $recursive     if true, the tree will be recursive
         * @param boolean   $show_root     if true, the root node will be displayed
         * @param string    $root_name     if the root node is the very root element, you can set its name here
         * @param boolean   $root_expand   if true, the root node will be expandable
         * @param boolean   $root_is_link  if true, the root node will be a link
         *
         * @retval string       HTML/Javascript string
         *
         * @throws Exception    if there was an error
         */
        public function build_javascript_tree(  $tree_name, $page, $parameter, $target = '', $recursive = true,
                                                $show_root = false, $root_name = 'Oberste Ebene',
                                                $root_expand = true, $root_is_link = true)
        {
            $javascript = array();
            $javascript[] = '<script type="text/javascript">';
            $javascript[] = "$tree_name = new dTree('$tree_name');";

            if ($show_root)
            {
                // the root node is visible
                if ($this->get_id() > 0)
                    $root_name = $this->get_name();

                if ($root_is_link)
                    $root_link = $page ."?". $parameter ."=".$this->get_id();
                else
                    $root_link = '';

                if ($root_expand)
                {
                    // we need a second (invisible) root node
                    $javascript[] = "$tree_name.add(". strval($this->get_parent_id()+1).",-1, '');";
                    $javascript[] = "$tree_name.add(". strval($this->get_id()+1) .",". strval($this->get_parent_id()+1) .
                                    ",'". $root_name ."', '".$root_link."', '', '".$target."');";
                }
                else
                {
                    $javascript[] = "$tree_name.add(". strval($this->get_id()+1) .",-1,'". $root_name ."', '".
                                    $root_link."', '', '".$target."');";
                }
            }
            else
            {
                // the root node is invisible
                $javascript[] = "$tree_name.add(". strval($this->get_id()+1).",-1, '');";
            }

            // get all subelements
            $subelements = $this->get_subelements($recursive);

            foreach ($subelements as $element)
            {
                $javascript[] = $tree_name.'.add('.strval($element->get_id()+1).','.strval($element->get_parent_id()+1).
                                ",'".addslashes(htmlentities($element->get_name(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'))."','".
                                $page.'?'.$parameter.'='.$element->get_id()."','','".$target."');";
            }

            $javascript[] = "document.write($tree_name);";
            $javascript[] = '</script>';
            $javascript[] = '<br>';
            $javascript[] = '<a href="javascript:'. $tree_name .'.openAll();">Alle Anzeigen</a> | ';
            $javascript[] = '<a href="javascript:'. $tree_name .'.closeAll();">Alle Schliessen</a>';

            return implode("\n", $javascript);
        }

        /**
         * @brief Build a HTML tree with all subcategories of this element
         *
         * This method prints a <option>-Line for every item.
         * <b>The <select>-tags are not printed here, you have to print them yourself!</b>
         * Deeper levels have more spaces in front.
         *
         * @param integer   $selected_id    the ID of the selected item
         * @param boolean   $recursive      if true, the tree will be recursive
         * @param boolean   $show_root      if true, the root node will be displayed
         * @param string    $root_name      if the root node is the very root element, you can set its name here
         *
         * @retval string       HTML string if success
         *
         * @throws Exception    if there was an error
         */
        public function build_html_tree($selected_id = NULL, $recursive = true,
                                        $show_root = true, $root_name = 'Oberste Ebene')
        {
            $html = array();

            if ($show_root)
            {
                $root_level = $this->get_level();
                if ($this->get_id() > 0)
                    $root_name = $this->get_name();

                $html[] = '<option value="'. $this->get_id() . '">'. $root_name .'</option>';
            }
            else
                $root_level =  $this->get_level() + 1;

            // get all subelements
            $subelements = $this->get_subelements($recursive);

            foreach ($subelements as $element)
            {
                $level = $element->get_level() - $root_level;
                $selected = ($element->get_id() == $selected_id) ? 'selected' : '';

                $html[] = '<option '. $selected .' value="'. $element->get_id() . '">';
                for ($i = 0; $i < $level; $i++)
                    $html[] = "&nbsp;&nbsp;&nbsp;";
                $html[] = $element->get_name() .'</option>';
            }

            return implode("\n", $html);
        }


        public function build_bootstrap_tree($page, $parameter, $recursive = false,
                                        $show_root = false, $use_db_root_name = true ,$root_name = 'Oberste Ebene')
        {
            $subelements = $this->get_subelements(false);
            $nodes = array();

            foreach ($subelements as $element)
            {
                $nodes[] = $element->build_bootstrap_tree($page,$parameter);
            }

            // if we are on root level?
            if($this->get_parent_id()==-1)
            {
                if($show_root)
                {
                    $tree = array(
                                array('text' => ($use_db_root_name) ? $this->get_name() : $root_name ,
                                'href' => $page ."?". $parameter ."=".$this->get_id(),
                                'nodes' => $nodes)
                    );
                }
                else //Dont show root node
                {
                    $tree = $nodes;
                }
            }
            else
            {
                if(!empty($nodes))
                {
                    $tree = array('text' => $this->get_name(),
                              'href' => $page ."?". $parameter ."=".$this->get_id(),
                              'nodes' => $nodes
                    );
                }
                else
                {
                    $tree = array('text' => $this->get_name(),
                                'href' => $page ."?". $parameter ."=".$this->get_id()
                    );
                }
            }


            return $tree;
        }

        /********************************************************************************
        *
        *   Static Methods
        *
        *********************************************************************************/

        /**
         * @copydoc DBElement::check_values_validity()
         */
        public static function check_values_validity(&$database, &$current_user, &$log, &$values, $is_new, &$element = NULL)
        {
            if ($values['parent_id'] == 0)
                $values['parent_id'] = NULL; // NULL is the root node

            // first, we let all parent classes to check the values
            parent::check_values_validity($database, $current_user, $log, $values, $is_new, $element);

            if (( ! $is_new) && ($values['id'] == 0))
                throw new Exception('Die Oberste Ebene kann nicht bearbeitet werden!');

            // with get_called_class() we can get the class of the element which will be edited/created.
            // example: if you write "$new_cat = Category::add(...);", get_called_class() returns "Category"
            $classname = get_called_class();

            // check "parent_id"
            if (( ! $is_new) && ($values['parent_id'] == $values['id']))
                throw new Exception('Ein Element kann nicht als Unterelement von sich selber zugeordnet werden!');

            try
            {
                $parent_element = new $classname($database, $current_user, $log, $values['parent_id'], true);
            }
            catch (Exception $e)
            {
                debug('warning', 'Ungültige "parent_id": "'.$values['parent_id'].'"'.
                        "\n\nUrsprüngliche Fehlermeldung: ".$e->getMessage(),
                        __FILE__, __LINE__, __METHOD__);
                throw new Exception('Das ausgewählte übergeordnete Element existiert nicht!');
            }

            // to avoid infinite parent_id loops (this is not the same as the "check parent_id" above!)
            if (( ! $is_new) && ($parent_element->get_parent_id() == $values['id']))
                throw new Exception('Ein Element kann nicht einem seiner direkten Unterelemente zugeordnet werden!');

            // check "name" + "parent_id" (the first check of "name" was already done by
            // "parent::check_values_validity", here we check only the combination of "parent_id" and "name")
            // we search for an element with the same name and parent ID, there shouldn't be one!
            $id = ($is_new) ? -1 : $values['id'];
            $query_data = $database->query( 'SELECT * FROM '. $parent_element->get_tablename() .
                                            ' WHERE name=? AND parent_id <=> ? AND id<>?',
                                            array($values['name'], $values['parent_id'], $id));
            if (count($query_data) > 0)
                throw new Exception('Es existiert bereits ein Element auf gleicher Ebene ('.$classname.'::'.
                                    $parent_element->get_full_path().') mit gleichem Namen ('.$values['name'].')!');
        }

    }

?>
