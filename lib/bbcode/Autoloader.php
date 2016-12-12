<?php
class BBCodeParser_Autoloader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'load_class'));
    }

    public static function register()
    {
        new BBCodeParser_Autoloader();
    }

    public function load_class($class_name)
    {
        $class_name = str_replace("Golonka\\BBCode","",$class_name);
        $file = __DIR__ . str_replace('\\', '/', $class_name) . '.php';
        if (file_exists($file)) {
            require_once($file);
        }
    }
}

BBCodeParser_Autoloader::register();