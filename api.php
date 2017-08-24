<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 24.08.2017
 * Time: 16:43
 */

include_once('start_session.php');

$app = new Slim\App();
$database           = new Database();
$log                = new Log($database);
$current_user       = new User($database, $current_user, $log, 1); // admin


/**
 * Creates a error message
 * @param \Slim\Http\Response $response The existing response object.
 * @param string|array $message The message of the error.
 * @param int $code The HTTP error code of the error.
 * @param Exception|array $exception An Exception that happened
 * @return \Slim\Http\Response The updated response Object
 */
function generateError($response, $message = "", $code = 500, $exception = null)
{
    if($message != "")
        $error = array("message" => $message);
    if($exception != null)
        $error = array("message" => $exception->getMessage());

    return $response->withJson(array("code" => $code, "errors" => array($error)), $code);
}

/********************************************************************
 * Category
 ********************************************************************/

$app->get("/1.0.0/category/{cid}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['cid'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $category = new Category($database, $current_user, $log, $args['cid']);
        return $response->withJson($category->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }

});

/********************************************************************
 * System
 ********************************************************************/

/**
 * Get the system version
 */
$app->get("/1.0.0/system/info", function($request, $response, $args) {
    $ver_str = SystemVersion::get_installed_version()->as_string();
    $data = array("version" => $ver_str,
        "gitBranch" => get_git_branch_name(), "gitCommit" => get_git_commit_hash());
    return $response->withJson($data);
});


$app->run();