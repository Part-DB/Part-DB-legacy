<?php

/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 04.01.2017
 * Time: 15:36
 */
abstract class EditPage extends ActionPage
{
    protected $add_more;

    protected function evaluate_action()
    {
        $action = 'default';
        if (isset($_REQUEST["add"]))                {$action = 'add';}
        if (isset($_REQUEST["delete"]))             {$action = 'delete';}
        if (isset($_REQUEST["delete_confirmed"]))   {$action = 'delete_confirmed';}
        if (isset($_REQUEST["apply"]))              {$action = 'apply';}
        return $action;
    }

    abstract protected function action_add($html);
    abstract protected function action_delete($html);
    abstract protected function action_delete_confirmed($html);
    abstract protected function action_apply($html);
}