<?php

abstract class Page
{
    /* The HTML Obj. which should be used for every Output of this Page */
    protected $html;
    /* The name of the page */
    protected $page_name;
    /* The objects that are needed always if we use the DB */
    protected $database, $log, $current_user;
    /* A array used to store messages which occurs on the execution of the Page */
    protected $messages,  $fatal_error;

    protected $file_name;

    public function __construct($page_name)
    {
        global $config;
        //Initialize the needed HTML objects
        $this->page_name = $page_name;
        $this->html = new HTML($config['html']['theme'], $config['html']['custom_css'], $page_name);

        //Initialize the message "system"
        $this->messages = array();
        $this->fatal_error = false;
    }

    /* In here the Page should initialize every objects its need permanently, like Part or Footprint objects */
    abstract protected function init_objects();
    /* Here the request should be evaluated and variables should be set, what and how to do */
    abstract protected function evaluate_requests();
    /* In this function the requested work should be done and the variables of the Templates should be set */
    abstract protected function do_work($html);
    /* Here should be the (HTML) output of the Page */
    abstract protected function print_templates($html);

    abstract protected function generate_reload_link();

    /**
     * Here every DB element gets initialized
     */
    protected function init_db_objects()
    {
        if(!$this->fatal_error) {
            try {
                $this->database = new Database();
                $this->log                = new Log($this->database);
                $this->current_user       = new User($this->database, $this->current_user, $this->log, 1); // admin
            }
            catch(Exception $e){
                $this->add_error($e->getMessage());
            }

        }
    }

    public function run()
    {

        //Create the DB Objects
        $this->init_db_objects();

        //Evaluate all requests
        try {
            $this->evaluate_requests();
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



    /** Adds the given message array to the global message array which will be outputed.
     * @param array $messages
     */
    protected function add_message($messages)
    {
        $this->messages = array_merge($this->messages, $messages);
    }

    /**
     * Adds an error to the message output
     * @param $content The content of the error message
     * @param $fatal True if the error should be fatal, so no other actions are executed
     */
    protected function add_error($content, $fatal = true)
    {
        $this->fatal_error = $fatal;
        debug("error", "Exception:", $content);
        $this->messages[] =  array('text' => nl2br($content), 'strong' => true, 'color' => 'red');
    }

}