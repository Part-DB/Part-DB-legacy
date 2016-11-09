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
        switch($mode)
        {
            case APIMode::TREE_CATEGORY:
                $data = $this->buildCategoryTree($params);
                break;
            case APIMode::TREE_DEVICES:
                $data = $this->buildDeviceTree($params);
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

    private function buildToolsTree()
    {
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


        return $tree;
    }


}