<?php

abstract class ActionPage extends Page
{
    //The action which should be executed
    protected $action = null;


    abstract protected function evaluate_action();

    /* This is called after every action, can be used for things that should be always done */
    abstract protected function action_shared($html);

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
                $this->action_shared($this->html);
            }
            catch (Exception $e)
            {
                //This errors are not fatal
                $this->add_error($e->getMessage());
            }
        }
    }

    /*
    public function __call($name, $arguments)
    {
        if(strpos($name,"action_")!=false)
        {
            $this->add_error(sprintf(_('Die gewünschte Action "%s" existiert nicht!'), $name));
        }
    } */


    protected function action_default($html)
    {
        //Do nothing on default
    }

    public function run()
    {
        //Create the DB Objects
        $this->init_db_objects();

        //Evaluate all requests
        try {
            $this->evaluate_requests();
            $this->action = $this->evaluate_action();
        }
        catch (Exception $e)
        {
            $this->add_error($e->getMessage());
        }

        //Create other objects
        if(!$this->fatal_error) {
            try {
                $this->init_objects();
            } catch (Exception $e) {
                $this->add_error($e->getMessage());
            }
        }

        if(!$this->fatal_error) {
            try {
                $this->do_work($this->html);
            } catch (Exception $e) {
                $this->add_error($e->getMessage());
            }
        }

        // Output the Page header with the messages
        $reload_link = $this->fatal_error ? $this->generate_reload_link() : '';
        $this->html->print_header($this->messages, $reload_link);

        //Output the templates only if no fatal error happened
        if(!$this->fatal_error)
            $this->print_templates($this->html);

        //Always print footer
        $this->html->print_footer();
    }

}