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
*/

namespace PartDB;

/**
 * @todo
 *   Soll der SysAdmin einen Datenbankeintrag haben? Mit Admin-Gruppe?
 *   Oder sollen die Rechte des Admins hardgecoded sein (ID = 0) (wie bei "StructuralDBElement")?
 *   Zweiteres wäre theoretisch schöner, da man die Adminrechte nicht verlieren kann durch eine
 *   kaputte Datenbank. Allerdings müsste das Admin-Passwort dann irgendwo gespeichert werden,
 *   wo man es auch bequem wieder ändern kann, vielleicht in $config (config.php)?
 *   Da momentan andere Sachen eine höhere Priorität haben als die Benutzerverwaltung,
 *   lasse ich das hier einfach mal so stehen, das kann man dann anschauen sobald es gebraucht wird.
 *   kami89
 */

use Exception;

/**
 * @file User.php
 * @brief class User
 *
 * @class User
 * All elements of this class are stored in the database table "users".
 * @author kami89
 */
class User extends Base\AttachementsContainingDBElement
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

    /** @var Group the group of this user */
    private $group = null;

    /********************************************************************************
     *
     *   Constructor / Destructor / reset_attributes()
     *
     *********************************************************************************/

    /**
     * Constructor
     *
     * @param Database      &$database      reference to the Database-object
     * @param User|NULL     &$current_user  @li reference to the current user which is logged in
     *                                      @li NULL if $id is the ID of the current user
     * @param Log           &$log           reference to the Log-object
     * @param integer       $id             ID of the user we want to get
     *
     * @throws Exception    if there is no such user in the database
     * @throws Exception    if there was an error
     */
    public function __construct(&$database, &$current_user, &$log, $id)
    {
        if (! is_object($current_user)) {     // this is that you can create an User-instance for first time
            $current_user = $this;
        }           // --> which one was first: the egg or the chicken? :-)

        //parent::__construct($database, $current_user, $log, 'users', $id);
    }

    /**
     * @copydoc DBElement::reset_attributes()
     */
    public function resetAttributes($all = false)
    {
        $this->group = null;

        parent::resetAttributes($all);
    }

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the group of this user
     *
     * @return Group        the group of this user
     *
     * @throws Exception    if there was an error
     */
    public function getGroup()
    {
        if (! is_object($this->group)) {
            $this->group = new Group(
                $this->database,
                $this->current_user,
                $this->log,
                $this->db_data['group_id']
            );
        }

        return $this->group;
    }

    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Change the group ID of this user
     *
     * @param integer $new_group_id     the ID of the new group
     *
     * @throws Exception if the new group ID is not valid
     * @throws Exception if there was an error
     */
    public function setGroupID($new_group_id)
    {
        $this->setAttributes(array('group_id' => $new_group_id));
    }

    /********************************************************************************
     *
     *   Static Methods
     *
     *********************************************************************************/

    /**
     * @copydoc DBElement::check_values_validity()
     */
    public static function checkValuesValidity(&$database, &$current_user, &$log, &$values, $is_new, &$element = null)
    {
        // first, we let all parent classes to check the values
        parent::checkValuesValidity($database, $current_user, $log, $values, $is_new, $element);

        // check "group_id"
        try {
            $group = new Group($database, $current_user, $log, $values['group_id']);
        } catch (Exception $e) {
            debug(
                'warning',
                _('Ungültige "group_id": "').$values['group_id'].'"'.
                _("\n\nUrsprüngliche Fehlermeldung: ").$e->getMessage(),
                __FILE__,
                __LINE__,
                __METHOD__
            );
            throw new Exception(_('Die gewählte Gruppe existiert nicht!'));
        }
    }

    /**
     * Get count of users
     *
     * @param Database &$database   reference to the Database-object
     *
     * @return integer              count of users
     *
     * @throws Exception            if there was an error
     */
    public static function getCount(&$database)
    {
        if (!$database instanceof Database) {
            throw new Exception(_('$database ist kein Database-Objekt!'));
        }

        return $database->getCountOfRecords('users');
    }
}
