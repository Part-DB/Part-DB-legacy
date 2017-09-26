<?php
/*
    Part-DB Version 0.4+ "nextgen"
    Copyright (C) 2017 Jan Böhmer
    https://github.com/jbtronics

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

namespace PartDB\Tools;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\StandardDebugBar;

class PDBDebugBar
{
    private static $singleton;
    private $debugbar;
    private $renderer;

    private function __construct()
    {
        $this->debugbar = new StandardDebugBar();
        $baseURL = BASE_RELATIVE . "/vendor/maximebf/debugbar/src/DebugBar/Resources";
        $this->renderer = $this->debugbar->getJavascriptRenderer($baseURL);
        global $config;
        $this->debugbar->addCollector(new ConfigCollector($config));
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
        if (!$this->debugbar->hasCollector("Database")) {
            $this->debugbar->addCollector(new PDOCollector($pdo));
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

    public static function isActivated()
    {
        global $config;
        return $config['debug']['debugbar'];
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
        if (is_null(PDBDebugBar::$singleton)) {
            PDBDebugBar::$singleton = new PDBDebugBar();
        }
        return PDBDebugBar::$singleton;
    }
}
