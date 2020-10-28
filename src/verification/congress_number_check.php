<?php
Namespace Drupal\nfb_washington\verification;
use Drupal\nfb_washington\database\base;

class  congress_number_check
{
    public $database;
    public $congress_number;
    public function get_congress()
    {return $this->congress_number;}
    public function congress_number_verification(&$form, $form_state){
        $this->congress_number_check();
        if($this->get_congress() == null)
        {$this->congress_number_not_found($form, $form_state);}
        else{ $this->congress_number_markup($form, $form_state);}
    }
    public function congress_number_check()
    {
        $congress_number = null;
        $query = "select * from nfb_washington_config where setting = 'congress_number' and active = '0';";
        $key = 'config_id';
        $this->database = new base();
        $this->database->select_query($query, $key);
        if($this->database->get_result() == "error"|| $this->database->get_result() == array())
        {
            foreach($this->database->get_result() as $setting)
            {
                if($congress_number == null){
                $setting = get_object_vars($setting);
                $congress_number = $setting['value'];}
            }
        }
        $this->congress_number = $congress_number;
    }
    public function congress_number_markup(&$form, $form_state)
    {
        $form['congress_number_markup'] = array(
            '#type' => "item",
            "#markup" => "<p><b>Current Congress Number: ".$this->get_congress()."</b></p>",
        );
    }
    public function congress_number_not_found(&$form, $form_state)
    {
        $form['congress_number_markup'] = array(
          '#type' => "item",
           '#makrup' => "<p class = 'admin_alert'><b>No Congress number set. Please visit <a href='nfb_washington/admin/configuration'>the configuration page</a> </b></p>",
        );
    }
}