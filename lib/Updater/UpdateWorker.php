<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 27.09.2017
 * Time: 12:16
 */

namespace PartDB\Updater;


class UpdateWorker
{
    /** @var UpdateStatus  */
    protected $update_status;

    /**
     * UpdateWorker constructor.
     * @param $update_status UpdateStatus
     */
    public function __construct(&$update_status)
    {
        $this->update_status = $update_status;
    }

    public function start()
    {
        if ($this->update_status->getDownloadLink() !== "" && $this->update_status->getDownloadTarget() !== "") {
            $this->download();
        }
    }

    protected function download()
    {
        if (!$this->update_status->getDownloading()) {
            //Ignore user aborts.
            ignore_user_abort(true);
            set_time_limit(0);

            $this->update_status->setDownloading(true);

            //Download Update from $link
            $link = $this->update_status->getDownloadLink();
            try {
                $ret = downloadFile($link, BASE . "/data/updater/", $this->update_status->getDownloadTarget(), true, 0);
                if ($ret !== false) {
                    $this->update_status->setDownloading(false);
                    $this->update_status->setDownloadLink("");
                    $this->update_status->setDownloadTarget("");
                }
            } catch (\Exception $ex){
                $this->update_status->setDownloading(false);
            }
        }
    }
}