<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 25.08.2017
 * Time: 17:47
 */

class PDBDebugBar
{
    static private $singleton;
    private $debugbar;
    private $renderer;

    private function __construct()
    {
        $this->debugbar = new \DebugBar\StandardDebugBar();
        $this->debugbar["messages"]->addMessage("Test");
        $this->debugbar->sendDataInHeaders();
        $baseURL = BASE_RELATIVE . "/vendor/maximebf/debugbar/src/DebugBar/Resources";
        $this->renderer = $this->debugbar->getJavascriptRenderer($baseURL);
    }

    public function getRenderer()
    {
        return $this->renderer;
    }

    public static function is_activated()
    {
        global $config;
        return $config['debug']['enable'];
    }

    /**
     * @return PDBDebugBar
     */
    public static function getInstance()
    {
        if(is_null(PDBDebugBar::$singleton))
        {
            PDBDebugBar::$singleton = new PDBDebugBar();
        }
        return PDBDebugBar::$singleton;
    }
}