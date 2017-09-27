<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 26.09.2017
 * Time: 17:52
 */

namespace PartDB\Updater;


class UpdateStatus
{
    protected $status_file_path;

    protected $data;


    public function __construct()
    {
        $this->status_file_path = BASE . "/data/updater/updater.json";
        $this->readFile();
    }

    protected function readFile()
    {
        if (file_exists($this->status_file_path)) {
            $content = file_get_contents($this->status_file_path);
            $this->data = json_decode($content, true);
        } else {
            $this->data = array();
        }
    }

    protected function saveFile()
    {
        $content = json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($this->status_file_path, $content);
    }

    /***
     * Getters
     */

    public function getDownloadLink()
    {
        if (!isset($this->data['download_link'])) {
            return "";
        }
        return $this->data['download_link'];
    }

    public function getDownloading()
    {
        if (!isset($this->data['downloading'])) {
            return false;
        }
        return $this->data['downloading'];
    }

    public function getDownloadTarget()
    {
        if (!isset($this->data['download_target'])) {
            return "";
        }
        return $this->data['download_target'];
    }

    /***********************
     * Setters
     ***********************/


    public function setDownloading($new_state)
    {
        $this->data['downloading'] = $new_state;
        $this->saveFile();
    }

    public function setDownloadLink($new_state)
    {
        $this->data['download_link'] = $new_state;
        $this->saveFile();
    }

    public function setDownloadTarget($target_path)
    {
        $this->data['download_target'] = $target_path;
        $this->saveFile();
    }

}