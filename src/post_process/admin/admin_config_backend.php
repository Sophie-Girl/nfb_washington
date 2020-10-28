<?php
Namespace Drupal\nfb_washington\post_process\admin;
use drupal\core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;

Class admin_config_backend
{
    public $query;
    public $api_key_value;
    public $congress_number_value;
    public function get_api_key_value()
    {return $this->api_key_value;}
    public function get_congress_number_value()
    {Return $this->congress_number_value;}
    public $database;
    public $user_name;
    public function get_user_name()
    {
        return $this->user_name;
    }
    public function admin_config_form_backend(FormStateInterface $form_state)
    {
        $this->set_form_values($form_state);
        $this->find_existing_api_key();
        $this->find_existing_congress_number();
    }
    public function set_form_values($form_state)
    {
        $this->api_key_value = $form_state->getValue("api_key");
        $this->congress_number_value = $form_state->getValue("congress_number");
    }
    public function set_user_name(){
        $user = \drupal::currentUser()->getUsername();
    }
    public function find_existing_api_key()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_config where setting = 'pp_id' and value = '".$this->get_api_key_value()."' ;";
        $key = "value";
        $this->database->select_query($query, $key);
        if($this->database->get_result() == "error") {
            $this->insert_new_row();
        }
        else
            {
                $this->update_existing_api_key();;
            }
    }
    public function update_existing_api_key(){
        $query = "update nfb_washington_config
        set value = '".$this->get_api_key_value()."'
        where setting = '"."pp_id"."' and config_id > '"."0"."';";
        $this->database = new base();
        $this->database->update_query($query);
    }
    public function insert_new_row()
    {
        $table = "nfb_washington_config";
        $fields = array(
            'setting' => "pp_id",
            'value' => $this->get_api_key_value(),
            "active" => "0",
            "created_user" => \drupal::currentUser()->getUsername(),
            "last_modified_user" => \drupal::currentUser()->getUsername(),
        );
        $this->database = new base();
        $this->database->insert_query($table, $fields);
    }
    public function find_existing_congress_number()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_config where setting = 'congress_number' and value = '".$this->get_congress_number_value()."';";
        $key = "value";
        $this->database->select_query($query, $key);
        if($this->database->get_result() == "error")
        {
            $this->insert_congress_number();
        }
        else
        {
            $this->update_congress_number();
        }
    }
    public function insert_congress_number()
    {
        $table = "nfb_washington_config";
        $fields = array(
            'setting' => "congress_number",
            'value' => $this->get_congress_number_value(),
            "active" => "0",
            "created_user" => \drupal::currentUser()->getUsername(),
            "last_modified_user" => \drupal::currentUser()->getUsername(),
        );
        $this->database = new base();
        $this->database->insert_query($table, $fields);
    }
    public function update_congress_number()
    {
        $query = "update nfb_washington_config
        set value = '".$this->get_congress_number_value()."'
        where setting = '"."congress_number"."' and config_id > '"."0"."';";
        $this->database = new base();
        $this->database->update_query($query);
    }

}