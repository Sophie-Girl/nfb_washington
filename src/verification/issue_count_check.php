<?php
Namespace Drupal\nfb_washington\verification;
use Drupal\nfb_washington\database\base;

class issue_count_check extends api_key_check
{
    public $issue_count;
    public function get_issue_count()
    {return $this->issue_count;}

    public function issue_count_verification(&$form, $form_state)
    {
        $this->check_issue_count();
        if($this->get_issue_count() == null)
        {
            $this->issue_count_not_found($form, $form_state);
        }
        else { $this->issue_count_markup($form, $form_state);}

    }
    public function check_issue_count()
    {
        $issue_count = null;
        $query = "select * from nfb_washington_config where setting = 'issue_count' and active = '0';";
        $key = 'config_id';
        $this->database = new base();
        $this->database->select_query($query, $key);
        if($this->database->get_result() != "error"|| $this->database->get_result() != array())
        {
            foreach($this->database->get_result() as $setting)
            {
                if($issue_count == null){
                    $setting = get_object_vars($setting);
                    $issue_count = $setting['value'];}
            }
        }
        $this->issue_count = $issue_count;
        $this->database = null;
    }
    public function issue_count_markup(&$form, $form_state)
    {
        $form['issue_count_markup'] = array(
            '#type' => "item",
            "#markup" => "<p><b>Current Issue Number Limit: ".$this->get_issue_count()."</b></p>",
        );
    }
    public function issue_count_not_found(&$form, $form_state)
    {
        $form['issue_count_markup'] = array(
            '#type' => "item",
            '#markup' => "<p class = 'admin_alert'><b>No issue number set. Please visit <a href='nfb_washington/admin/configuration'>the configuration page</a> </b></p>",
        );
    }
}