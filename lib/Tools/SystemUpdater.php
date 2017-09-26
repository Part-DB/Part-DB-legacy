<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 26.09.2017
 * Time: 15:16
 */

namespace PartDB\Tools;


class SystemUpdater
{
    /** Use this channel, if you only want to get stable versions. (Releases from GitHub) */
    const CHANNEL_STABLE = "stable";
    /** Use this channel, if you want to get always the newest version (nextgen branch from GitHub) */
    const CHANNEL_DEV    = "dev";

    /** @var string */
    protected $channel = "";

    private $github_repo = "jbtronics/Part-DB";

    public function __construct($channel)
    {
        $this->channel = $channel;
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
            return $response[0]['commit']['author']['date'];
        } else {
            return substr($response[0]['sha'], 0,6);
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
        //Ignore user aborts.
        ignore_user_abort(true);
        set_time_limit(0);

        //Generate Update-Link
        if ($this->channel == static::CHANNEL_STABLE) {
            $newestVersion = $this->getLatestVersionName();
            $link = "https://github.com/' . $this->github_repo . '/archive/" . $newestVersion . ".zip";
        } elseif ($this->channel == static::CHANNEL_DEV) {
            $link = "https://github.com/jbtronics/Part-DB/archive/nextgen.zip";
        }

        //Download Update from $link
        if (isset($link)) {
            return downloadFile($link, BASE . "/data/updater/", $this->buildUpdateDownloadTargetPath());
        }

        throw new \Exception(_("Ungültige Version"));
    }
}