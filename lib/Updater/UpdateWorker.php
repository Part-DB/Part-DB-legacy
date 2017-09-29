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

        //Ignore user aborts.
        ignore_user_abort(true);
        set_time_limit(0);

        $this->update_status->setUpdating(true);
        $path = $this->update_status->getUpdateSource();

        $excludes = array();
        $excludes[] = "data";
        $excludes[] = "vendor";
        $excludes[] = ".idea";
        $excludes[] = "user_settings.php";
        $excludes[] = "update_worker.php";

        //Remove all old files, so we can extract the new ones.
        $this->rmDirRecursive(BASE, $excludes);
        //Unzip the new version.

        $zipExcludes[] = "data";
        $zipExcludes[] = "vendor";
        $zipExcludes[] = ".idea";

        $this->unzipUpdate(BASE , $path, $zipExcludes);

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
                    //Check for unzipExceptions
                    $name = $zip->getNameIndex($i);
                    foreach ($unzipExceptions as $exception) {
                        if (strpos($name, $exception)) {
                            continue;
                        }
                    }
                    $zipPaths[] = $name;
                }

                //Extract files from ZIP archive
                $zip->extractTo($dirname, $zipPaths);

                $zip->close();

                //Check if we need to move files, because all files are in a Part-DB-* folder.
                $files = scandir($dirname);
                $movedir = false;
                foreach ($files as $file) {
                    if (strpos($file, "Part-DB-") !== false) {
                        $movedir = $file;
                    }
                }
                //When needed move files and clean up
                if ($movedir !== false) {
                    static::rmove($dirname . "/" . $movedir, $dirname);
                    rmdir($dirname . "/" . $movedir);
                }


                //Delete ZIP-File
                unlink($updatePath);

                return true;
            } else {
                //open zip failed
                return false;
            }
        }
    }

    /**
     * Recursively move files from one directory to another
     *
     * @param String $src - Source of files being moved
     * @param String $dest - Destination of files being moved
     */
    protected function rmove($src, $dest)
    {

        // If source is not a directory stop processing
        if (!is_dir($src)) return false;

        // If the destination directory does not exist create it
        if (!is_dir($dest)) {
            if (!mkdir($dest)) {
                // If the destination directory could not be created stop processing
                return false;
            }
        }

        // Open the source directory to read in files
        $i = new \DirectoryIterator($src);
        foreach($i as $f) {
            if ($f->isFile()) {
                rename($f->getRealPath(), "$dest/" . $f->getFilename());
            } elseif (!$f->isDot() && $f->isDir()) {
                rmove($f->getRealPath(), "$dest/$f");
                rmdir($f->getRealPath());
            }
        }
        unlink($src);
    }
}