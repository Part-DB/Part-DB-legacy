<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 27.09.2017
 * Time: 12:35
 */

include_once('start_session.php');

use PartDB\Updater\UpdateWorker;
use PartDB\Updater\UpdateStatus;

$status = new UpdateStatus();
$worker = new UpdateWorker($status);
$worker->start();
