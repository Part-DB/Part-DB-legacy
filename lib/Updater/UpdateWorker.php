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
    /** @var UpdateStatus */
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
        if ($this->update_status->getUpdateSource() !== "") {
            $this->update();
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
            } catch (\Exception $ex) {
                $this->update_status->setDownloading(false);
            }
        }
    }

    protected function update()
    {
        //Abort if update is already in progress.
        if ($this->update_status->getUpdating()) {
            return;
        }

        $this->update_status->setUpdating(true);
        $path = $this->update_status->getUpdateSource();

        $excludes = array();
        $excludes[] = "data";
        $excludes[] = "vendor";
        $excludes[] = ".idea";
        $excludes[] = "user_settings.php";
        $excludes[] = "update_worker.php";

        //Remove all old files, so we can extract the new ones.
        //$this->rmDirRecursive(BASE, $excludes);
        //Unzip the new version.
        $this->unzipUpdate(BASE . "/tmp", $path, $excludes);

        $this->update_status->setUpdateSource("");
        $this->update_status->setUpdating(false);
    }

    //Deletes recursive all data from the path $dirname except the files and folders listet in array $deleteExceptions
    protected static function rmDirRecursive($dirname, $deleteExceptions)
    {
        // Sanity check
        if (!file_exists($dirname)) {
            return false;
        }

        // Simple delete for a file
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }

        // Loop through the folder
        $dir = dir($dirname);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..' || $entry == ".git" || in_array($entry, $deleteExceptions)) {
                continue;
            }

            // Recurse
            static::rmDirRecursive($dirname . DIRECTORY_SEPARATOR . $entry, $deleteExceptions);
        }

        // Clean up
        $dir->close();

        if ($dirname != getcwd()) {
            return rmdir($dirname);
        }
    }

    //Writes the new data from $newestVersion.zip to $dirname except the files and folders listet in array $unzipExceptions
    protected function unzipUpdate($dirname, $updatePath, $unzipExceptions)
    {
        //Check for Update-File
        if (!file_exists($updatePath)) {
            return 'Keine ZIP-Archive gefunden';
        } //Proceed with Update
        else {
            $zipPaths = array();
            $zip = new \ZipArchive;

            //Reading all Paths from ZIP
            if ($zip->open($updatePath) == true) {
                for ($i = 1; $i < $zip->numFiles; $i++) {
                    $zipPaths[] = $zip->getNameIndex($i);
                }

                //Alle PATHS durchlaufen
                foreach ($zipPaths as $zipPath) {
                    foreach ($unzipExceptions as $exception) {
                        if (strpos($zipPath, $exception) !== false) {
                            //zipPath Ã¼berspringen da in Ausnahme enthalten
                            $skip = true;
                        }
                    }

                    //UNZIP
                    //if $zipPath is a file, move that file from zip to target
                    if (!(substr($zipPath, -1) == '/') && !$skip) {
                        //Generate UnZip-Target Path
                        $target = $dirname . '/' . explode('/', $zipPath, 2)[1];

                        $fp = $zip->getStream($zipPath);
                        $ofp = fopen($target, 'w');

                        while (!feof($fp)) {
                            fwrite($ofp, fread($fp, 8192));
                        }
                        fclose($fp);
                        fclose($ofp);

                        //Set rights for files
                        chmod($target, 0444);
                    } //if $zipPath is a folder, create a new folder to $target
                    elseif ((substr($zipPath, -1) == '/') && !$skip) {
                        $target = $dirname . '/' . explode('/', $zipPath, 2)[1];
                        mkdir($target, 0555, true);
                    }
                    $skip = FALSE;
                }
                $zip->close();

                //Delete ZIP-File
                unlink($updatePath);

                return TRUE;
            } else {
                //open zip failed
                return FALSE;
            }
        }
    }
}