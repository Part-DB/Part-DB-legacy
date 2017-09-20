<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 13.09.2017
 * Time: 12:51
 */

namespace PartDB\Permissions;


use PartDB\Base\DBElement;
use PartDB\Interfaces\IHasPermissions;
use PartDB\Permissions\BasePermission;
use PartDB\Permissions\StructuralPermission;
use Symfony\Component\VarDumper\Cloner\Stub;

class PermissionManager
{
    /** @var IHasPermissions  */
    protected $perm_holder;
    /** @var  PermissionGroup[] */
    protected $permissions;

    const STORELOCATIONS    = "storelocations";
    const FOOTRPINTS        = "footprints";
    const CATEGORIES        = "categories";
    const SUPPLIERS         = "suppliers";
    const MANUFACTURERS     = "manufacturers";
    const DEVICES           = "devices";
    const ATTACHEMENT_TYPES = "attachement_types";

    const TOOLS             = "tools";

    const PARTS             = "parts";
    const PARTS_NAME        = "parts_name";
    const PARTS_DESCRIPTION = "parts_description";
    const PARTS_INSTOCK     = "parts_instock";
    const PARTS_MININSTOCK  = "parts_mininstock";
    const PARTS_FOOTPRINT   = "parts_footprint";
    const PARTS_COMMENT     = "parts_comment";
    const PARTS_STORELOCATION = "parts_storelocation";
    const PARTS_MANUFACTURER = "parts_manufacturer";
    const PARTS_ORDERDETAILS = "parts_orderdetails";
    const PARTS_PRICES      = "parts_prices";
    const PARTS_ATTACHEMENTS = "parts_attachements";
    const PARTS_ORDER        = "parts_order";

    const GROUPS            = "system_group_others";

    const DEVICE_PARTS      = "devices_parts";


    /**
     * PermissionManager constructor.
     * @param $perm_holder IHasPermissions A object which has permissions properties and which should be used for read/write.
     *                  Use null, when you want to return default values.
     */
    public function __construct(&$perm_holder)
    {
        $this->perm_holder = $perm_holder;
        $this->fillPermissionsArray();
        $this->permissions = array();

        $this->fillPermissionsArray();
    }

    /**
     * Generates a template loop for smarty_permissions.tpl (the permissions table).
     * @param $read_only boolean When true, all checkboxes are disabled (greyed out)
     * @param $inherit boolean If true, inherit values, are resolved.
     * @return array The loop for the permissions table.
     */
    public function generatePermissionsLoop($read_only = false, $inherit = false)
    {
        $loop = array();
        foreach ($this->permissions as $perm_group) {
            $loop[] = $perm_group->generatePermissionsLoop($read_only, $inherit);
        }

        return $loop;
    }

    /**
     * Takes a $_REQUEST array and parse permissions from it. Use it in combination with the smarty_permissions.tpl Template.
     * @param $request_array array The request array which should be parsed.
     */
    public function parsePermissionsFromRequest($request_array)
    {
        foreach ($request_array as $request => $value) {
            //The request variable is a permission when it begins with perm/
            if (strpos($request, "perm/") !== false) {
                try {
                    //Split the $name string into the different parts.
                    $tmp = explode("/", $request);
                    $permission = $tmp[1];
                    $operation  = $tmp[2];

                    //Get permession object.
                    $perm = $this->getPermission($permission);
                    //Set Value of the operation.
                    $perm->setValue($operation, parseTristateCheckbox($value));

                } catch (\Exception $ex) {
                    //Ignore exceptions. Dont do anything.
                }
            }
        }
    }

    /**
     * Gets the value of the Permission.
     * @param $perm_name string The name of the permission.
     * @param $perm_op string The name of the operation.
     * @param bool $inheritance When this is true, than inherit values gets resolved.
     *      Set this to false, when you want to get only the value of the permission, and not to resolve inherit values.
     * @return int The value of the requested permission.
     */
    public function getPermissionValue($perm_name, $perm_op, $inheritance = true)
    {
        $perm = $this->getPermission($perm_name);
        $val = $perm->getValue($perm_op);
        if ($inheritance == false) { //When no inheritance is needed, simply return the value.
            return $val;
        } else {
            if ($val == BasePermission::INHERIT) {
                $parent = $this->perm_holder->getParentPermissionManager(); //Get the parent permission manager.
                if ($parent == null) { //When no parent exists, than return current value.
                    return $val;
                }
                //Repeat the request for the parent.
                return $parent->getPermissionValue($perm_name, $perm_op, true);
            }
            return $val;
        }
    }

    /**
     * Returns the permission object for the permission with given name.
     * @param $name string The name of the requested permission.
     * @return BasePermission The requeste
     */
    public function &getPermission($name)
    {
        foreach ($this->permissions as $perm_group) {
            $perms = $perm_group->getPermissions();
            foreach ($perms as $perm) {
                if ($perm->getName() == $name) {
                    return $perm;
                }
            }
        }

        throw new \InvalidArgumentException(_("Keine Permission mit dem gegebenen Namen vorhanden!"));
    }

    /**
     * Gets the title of the permission Group, in which the permission with the given name is.
     * @param $perm_name string The name of the permissions, whichs permission group title should be determined.
     * @return string The title of the permission group.
     */
    public function getPermGroupTitle($perm_name)
    {
        foreach ($this->permissions as $perm_group) {
            $perms = $perm_group->getPermissions();
            foreach ($perms as $perm) {
                if ($perm->getName() == $perm_name) {
                    return $perm_group->getTitle();
                }
            }
        }

        throw new \InvalidArgumentException(_("Keine Permission mit dem gegebenen Namen vorhanden!"));
    }


    /**
     * Add all wanted permissions to $this->permissions.
     * If you want to add a new permission, then do it here.
     */
    protected function fillPermissionsArray()
    {
        $part_permissions       = array();
        $part_permissions[]     = new PartPermission($this->perm_holder, static::PARTS, _("Allgemein"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_NAME, _("Name"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_DESCRIPTION, _("Beschreibung"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_COMMENT, _("Kommentar"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_INSTOCK, _("Vorhanden"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_MININSTOCK, _("Min. Bestand"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_STORELOCATION, _("Lagerort"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_MANUFACTURER, _("Hersteller"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_FOOTPRINT, _("Footprint"));
        $part_permissions[]     = new CPartAttributePermission($this->perm_holder, static::PARTS_ATTACHEMENTS, _("Dateianhänge"));
        $part_permissions[]     = new CPartAttributePermission($this->perm_holder, static::PARTS_ORDERDETAILS, _("Bestellinformationen"));
        $part_permissions[]     = new CPartAttributePermission($this->perm_holder, static::PARTS_PRICES, _("Preise"));
        $part_permissions[]     = new PartAttributePermission($this->perm_holder, static::PARTS_ORDER, _("Bestellungen"));

        $this->permissions[] = new PermissionGroup(_("Bauteile"), $part_permissions);

        $structural_permissions = array();
        $structural_permissions[] = new StructuralPermission($this->perm_holder, static::STORELOCATIONS, _("Lagerorte"));
        $structural_permissions[] = new StructuralPermission($this->perm_holder, static::FOOTRPINTS, _("Footprints"));
        $structural_permissions[] = new StructuralPermission($this->perm_holder, static::CATEGORIES, _("Kategorien"));
        $structural_permissions[] = new StructuralPermission($this->perm_holder, static::SUPPLIERS, _("Lieferanten"));
        $structural_permissions[] = new StructuralPermission($this->perm_holder, static::MANUFACTURERS, _("Hersteller"));
        $structural_permissions[] = new StructuralPermission($this->perm_holder, static::DEVICES, _("Baugruppen"));
        $structural_permissions[] = new StructuralPermission($this->perm_holder, static::ATTACHEMENT_TYPES, _("Dateianhänge"));
        $this->permissions[] = new PermissionGroup(_("Datenstrukturen"), $structural_permissions);

        $misc_permissions = array();
        $misc_permissions[] = new ToolsPermission($this->perm_holder, static::TOOLS, _("Tools"));
        $misc_permissions[] = new StructuralPermission($this->perm_holder, static::GROUPS, _("Gruppen"));
        $misc_permissions[] = new DevicePartPermission($this->perm_holder, static::DEVICE_PARTS, _("Baugruppenbauteile"));
        $this->permissions[] = new PermissionGroup(_("Verschiedenes"), $misc_permissions);
    }

    /*******************************************************
     * Static functions
     *******************************************************/

    public static function defaultPermissionsLoop()
    {
        //Create a temp object for pass by reference.
        $tmp = null;
        $manager = new static($tmp);
        return $manager->generatePermissionsLoop();

    }
}