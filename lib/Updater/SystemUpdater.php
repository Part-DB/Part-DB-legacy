<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 26.09.2017
 * Time: 15:16
 */

namespace PartDB\Updater;


use PartDB\Updater\UpdateStatus;

class SystemUpdater
{
    /** Use this channel, if you only want to get stable versions. (Releases from GitHub) */
    const CHANNEL_STABLE = "stable";
    /** Use this channel, if you want to get always the newest version (nextgen branch from GitHub) */
    const CHANNEL_DEV    = "dev";

    /** @var string */
    protected $channel = "";

    private $github_repo = "jbtronics/Part-DB";

    protected $update_status;

    public function __construct($channel)
    {
        $this->channel = $channel;
        $this->update_status = new UpdateStatus();
    }

    public function isUpdateAvailable()
    {
        return true;
    }


    protected function getLatestVersionName()
    {
        if ($this->channel == static::CHANNEL_DEV) {
            $API_CALL = 'https://api.github.com/repos/' . $this->github_repo . '/commits';
        } else {
            $API_CALL = 'https://api.github.com/repos/' . $this->github_repo . '/releases';
        }

        //Check on GitHub for new Update
        $context  = stream_context_create(array('http' => array('user_agent' => 'Get Releases')));
        $response = json_decode(file_get_contents($API_CALL, false, $context), true);

        if ($this->channel == static::CHANNEL_DEV) {
            return substr($response[0]['sha'], 0,6);
        } else {
            return $response[0]['tag_name'];
        }
    }

    /**
     * Generates an (absolute) path to the current update version's ZIP archive.
     * @return string The absolute path to ZIP.
     */
    protected function buildUpdateDownloadTargetPath()
    {
        //Format of filename e.g. update_stable
        $file = "update_" . $this->channel . "_" . $this->getLatestVersionName() . ".zip";
        return  filter_filename($file);
    }


    public function downloadUpdate()
    {
        //Generate Update-Link
        if ($this->channel == static::CHANNEL_STABLE) {
            $newestVersion = $this->getLatestVersionName();
            $link = "https://github.com/' . $this->github_repo . '/archive/" . $newestVersion . ".zip";
        } elseif ($this->channel == static::CHANNEL_DEV) {
            $link = "https://github.com/jbtronics/Part-DB/archive/nextgen.zip";
        }

        if (isset($link)) {
            $this->update_status->setDownloadLink($link);
            $this->update_status->setDownloadTarget($this->buildUpdateDownloadTargetPath());
        } else {
            $this->update_status->setDownloadLink("");
        }
    }
}