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

    protected function do_work($html)
    {
        $call_name = "action_".$this->action;
        try {
            if(isset($this->action)) {
                if(true )//is_callable($call_name))
                {
                    $this->$call_name($html);
                }
                else {
                    throw new Exception(sprintf(_('Die gewünschte Action "%s" existiert nicht!'), $this->action));
                }
            }
        }
        catch (Exception $e)
        {
            //This errors are not fatal
            $this->add_error($e->getMessage(), false);
        }

        if(!$this->fatal_error)
        {
            try {
                $html->set_variable('add_more', $this->add_more, 'boolean');
                $this->action_shared($this->html);
            }
            catch (Exception $e)
            {
                //This errors are not fatal
                $this->add_error($e->getMessage());
            }
        }
    }
}