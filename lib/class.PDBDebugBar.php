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
        $baseURL = BASE_RELATIVE . "/vendor/maximebf/debugbar/src/DebugBar/Resources";
        $this->renderer = $this->debugbar->getJavascriptRenderer($baseURL);
        global $config;
        $this->debugbar->addCollector(new \DebugBar\DataCollector\ConfigCollector($config));
        $this->debugbar->addCollector(new DebugBar\DataCollector\TimeDataCollector());
    }

    public function &getRenderer()
    {
        return $this->renderer;
    }

    public function &getDebugBar()
    {
        return $this->debugbar;
    }

    public function registerPDO(&$pdo)
    {
        if(!$this->debugbar->hasCollector("Database"))
        {
            $this->debugbar->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($pdo));
        }
    }

    public function startMeasure($id, $description)
    {
        $this->debugbar['time']->startMeasure($id, $description);
    }

    public function stopMeasure($id)
    {
        $this->debugbar['time']->stopMeasure($id);
    }

    public static function is_activated()
    {
        global $config;
        return $config['debug']['enable'];
    }

    public function sendData()
    {
        $this->debugbar->sendDataInHeaders();
    }

    /**
     * @return PDBDebugBar
     */
    public static function &getInstance()
    {
        if(is_null(PDBDebugBar::$singleton))
        {
            PDBDebugBar::$singleton = new PDBDebugBar();
        }
        return PDBDebugBar::$singleton;
    }
}