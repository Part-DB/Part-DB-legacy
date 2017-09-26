<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 26.09.2017
 * Time: 15:13
 */

//Check for Update - return newest version
//if $newestVersion == nextgen it returns date of last commit in nextgen branch (for example: 2017-09-16T18:01:51Z)
//if $newestVersion != nextgen it returns the version of the latest release.
function checkUpdate($newestVersion)
{
    if($newestVersion == 'nextgen')
    {
        $API_CALL = 'https://api.github.com/repos/jbtronics/Part-DB/commits';
    }
    else
    {
        $API_CALL = 'https://api.github.com/repos/jbtronics/Part-DB/releases';
    }

    //Check on GitHub for new Update
    $context  = stream_context_create(array('http' => array('user_agent' => 'Get Releases')));
    $response = json_decode(file_get_contents($API_CALL, false, $context), true);

    if($newestVersion == 'nextgen')
    {
        return $response[0]['commit']['author']['date'];
    }
    else
    {
        return $response[0]['tag_name'];
    }
}

//Download zipped update from source - returns downloaded bytes, error, or 'already downloaded'
function downloadUpdate($newestVersion)
{
    //Generate Update-Link
    if($newestVersion != 'nextgen')
    {
        if(file_exists($newestVersion . '.zip'))
        {
            return 'Update bereits heruntergeladen.';
        }
        else
        {
            $link = "https://github.com/jbtronics/Part-DB/archive/" . $newestVersion . ".zip";
        }
    }
    elseif($newestVersion == 'nextgen')
    {
        $link = "https://github.com/jbtronics/Part-DB/archive/nextgen.zip";
    }

    //Download Update from $link
    if(isset($link))
    {
        return file_put_contents($newestVersion . '.zip',file_get_contents($link));
    }
    else
    {
        return false;
    }
}

//Deletes recursive all data from the path $dirname except the files and folders listet in array $deleteExceptions
function rmdirr($dirname, $deleteExceptions)
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
        if ($entry == '.' || $entry == '..' || $entry == 'test.php' || in_array($entry, $deleteExceptions)) {
            continue;
        }

        // Recurse
        rmdirr($dirname . DIRECTORY_SEPARATOR . $entry, $deleteExceptions);
    }

    // Clean up
    $dir->close();

    if($dirname != getcwd())
    {
        return rmdir($dirname);
    }
}

//Writes the new data from $newestVersion.zip to $dirname except the files and folders listet in array $unzipExceptions
function unzipUpdate($dirname, $newestVersion, $unzipExceptions)
{
    $updatePath = $newestVersion . '.zip';

    //Check for Update-File
    if(!file_exists($updatePath))
    {
        return 'Keine ZIP-Archive gefunden';
    }
    //Proceed with Update
    else
    {
        $zipPaths = array();
        $zip = new ZipArchive;

        //Reading all Paths from ZIP
        if($zip->open($updatePath) == TRUE)
        {
            for($i = 1; $i < $zip->numFiles; $i++)
            {
                $zipPaths[] = $zip->getNameIndex($i);
            }

            //Alle PATHS durchlaufen
            foreach($zipPaths as $zipPath)
            {
                foreach($unzipExceptions as $exception)
                {
                    if(strpos($zipPath, $exception) !== false)
                    {
                        //zipPath Ã¼berspringen da in Ausnahme enthalten
                        $skip = TRUE;
                    }
                }

                //UNZIP
                //if $zipPath is a file, move that file from zip to target
                if(!(substr($zipPath, -1) == '/') && !$skip)
                {
                    //Generate UnZip-Target Path
                    $target = $dirname . '/' . explode('/', $zipPath, 2)[1];

                    $fp = $zip->getStream($zipPath);
                    $ofp = fopen($target, 'w');

                    while(!feof($fp))
                    {
                        fwrite($ofp, fread($fp, 8192));
                    }
                    fclose($fp);
                    fclose($ofp);

                    //Set rights for files
                    chmod($target, 0444);
                }
                //if $zipPath is a folder, create a new folder to $target
                elseif((substr($zipPath, -1) == '/') && !$skip)
                {
                    $target = $dirname . '/' . explode('/', $zipPath, 2)[1];
                    mkdir($target, 0555, true);
                }
                $skip = FALSE;
            }
            $zip->close();

            //Delete ZIP-File
            unlink($updatePath);

            return TRUE;
        }
        else
        {
            //open zip failed
            return FALSE;
        }
    }
}