<?php

/**
 * Class containing abstract functions for building API outputs.
 *
 * class description.
 *
 * @version 1.0
 * @author jbtronics
 */
class API
{
    const OUTPUT_JSON = 1;

    private $format;
    private $pretty;
    private $database;
    private $current_user;
    private $log;

    public function __construct($database, $current_user, $log, $format = API::OUTPUT_JSON, $pretty=true)
    {
        $this->database = $database;
        $this->current_user = $current_user;
        $this->log = $log;

        $this->format = $format;
        $this->pretty = $pretty;
    }

    public function output($mode, $params)
    {
        try
        {
            switch($mode)
            {
                case APIMode::TREE_CATEGORY:
                    $data = $this->buildCategoryTree($params);
                    break;
                case APIMode::TREE_DEVICES:
                    $data = $this->buildDeviceTree($params);
                    break;
                case APIMode::TREE_TOOLS:
                    $data = $this->buildToolsTree($params);
                    break;
                case APIMode::GET_PART_INFO:
                    $data = $this->getPartInfo($params);
                    break;

                default:
                    $data = $this->makeError("Invalid action mode!");
            }

            if($this->format == API::OUTPUT_JSON)
            {
                if($this->pretty)
                    return json_encode($data, JSON_PRETTY_PRINT);
                else
                    return json_encode($data);
            }
            else
            {
                return "Invalid output format!";
            }
        }
        catch(Exception $ex)
        {
            return json_encode($this->makeError($ex), JSON_PRETTY_PRINT);
        }
    }

    private function makeError($msg)
    {
        $error = array('msg' => $msg);
        return array('error' => $error);
    }

    private function buildCategoryTree($params)
    {
        try
        {
            $root_category  = new Category($this->database, $this->current_user, $this->log, 0);
            if(isset($params['page']) && isset($params['parameter']))
            {
                return $root_category->build_bootstrap_tree($params['page'],$params['parameter']);
            }
            else
            {
                return $root_category->build_bootstrap_tree('show_category_parts.php','cid');
            }
        }
        catch (Exception $e)
        {
            return $this->makeError($e->getMessage());
        }

    }

    private function buildDeviceTree($params)
    {
        try
        {
            $root_device  = new Device($this->database, $this->current_user, $this->log, 0);
            if(isset($params['page']) && isset($params['parameter']))
            {
                return $root_device->build_bootstrap_tree($params['page'],$params['parameter']);
            }
            else
            {
                return $root_device->build_bootstrap_tree('show_device_parts.php','device_id');
            }
        }
        catch (Exception $e)
        {
            return $this->makeError($e->getMessage());
        }

    }

    private function buildToolsTree($params)
    {
        global $config;

        $disable_footprint          =   $config['footprints']['disable'];
        $disable_manufactur         =   $config['manufacturers']['disable'];
        $disable_devices            =   $config['devices']['disable'];
        $disable_help               =   $config['menu']['disable_help'];
        $disable_config             =   $config['menu']['disable_config'];
        $enable_debug_link          =   $config['menu']['enable_debug'];
        $disable_labels             =   $config['menu']['disable_labels'];
        $disable_calculator         =   $config['menu']['disable_calculator'];
        $disable_iclogos            =   $config['menu']['disable_iclogos'];
        $disable_tools_footprints   =   $config['menu']['disable_footprints'];
        $developer_mode             =   $config['developer_mode'];
        $db_backup_name             =   $config['db']['backup']['name'];
        $db_backup_url              =   $config['db']['backup']['url'];


        //Tools nodes
        $tools_nodes = array();
        $tools_nodes[] = treeview_node(_("Import"),BASE_RELATIVE."/tools_import.php");
        if(!$disable_labels) $tools_nodes[] = treeview_node(_("Labels"),BASE_RELATIVE."/tools_labels.php");
        if(!$disable_calculator) $tools_nodes[] = treeview_node(_("Widerstandsrechner"),BASE_RELATIVE."/tools_calculator.php");
        if(!$disable_footprint) $tools_nodes[] = treeview_node(_("Footprints"),BASE_RELATIVE."/tools_footprints.php");
        if(!$disable_iclogos) $tools_nodes[] = treeview_node(_("IC-Logos"),BASE_RELATIVE."/tools_iclogos.php");

        //System nodes
        $system_nodes = array();
        $system_nodes[] = treeview_node(_("Konfiguration"),BASE_RELATIVE."/system_config.php");
        $system_nodes[] = treeview_node(_("Datenbank"),BASE_RELATIVE."/system_database.php");

        //Show nodes
        $show_nodes = array();
        $show_nodes[] = treeview_node(_("Zu bestellende Teile"),BASE_RELATIVE."/show_order_parts.php");
        $show_nodes[] = treeview_node(_("Teile ohne Preis"),BASE_RELATIVE."/show_noprice_parts.php");
        $show_nodes[] = treeview_node(_("Obsolente Bauteile"),BASE_RELATIVE."/show_obsolete_parts.php");
        $show_nodes[] = treeview_node(_("Statistik"),BASE_RELATIVE."/statistics.php");

        //Edit nodes
        $edit_nodes = array();
        $edit_nodes[] = treeview_node(_("Lagerorte"),BASE_RELATIVE."/edit_storelocations.php");
        $edit_nodes[] = treeview_node(_("Footprints"),BASE_RELATIVE."/edit_footprints.php");
        $edit_nodes[] = treeview_node(_("Kategorien"),BASE_RELATIVE."/edit_categories.php");
        $edit_nodes[] = treeview_node(_("Lieferanten"),BASE_RELATIVE."/edit_suppliers.php");
        $edit_nodes[] = treeview_node(_("Dateitypen"),BASE_RELATIVE."/edit_attachement_types.php");

        //Add nodes to root
        $tree = array();
        $tree[] = treeview_node(_("Tools"),null,$tools_nodes);
        $tree[] = treeview_node(_("Bearbeiten"),null,$edit_nodes);
        $tree[] = treeview_node(_("Zeige"),null,$show_nodes);
        $tree[] = treeview_node(_("System"),null,$system_nodes);
        $tree[] = treeview_node(_("Hilfe"),BASE_RELATIVE."documentation/dokuwiki/index.php",null);

        return $tree;
    }

    private function getPartInfo($params)
    {
        if(!isset($params['pid']))
        {
            throw new Exception("You must specify a part id, with the 'pid' param!");
        }

        $part_id = (integer) $params['pid'];

        $part               = new Part($this->database, $this->current_user, $this->log, $part_id);
        $footprint          = $part->get_footprint();
        $storelocation      = $part->get_storelocation();
        $manufacturer       = $part->get_manufacturer();
        $category           = $part->get_category();
        $all_orderdetails   = $part->get_orderdetails();

        return $part;

    }





}