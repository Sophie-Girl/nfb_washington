<?php
Namespace Drupal\nfb_washington\post_process\admin;
use drupal\core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;

Class admin_config
{
    public $query;
    public $api_key_value;
    public $congress_number_value;
    public function get_api_key_value()
    {return $this->api_key_value;}
    public function get_congress_number_value()
    {Return $this->congress_number_value;}
    public $database;
    public function admin_form_backend(FormStateInterface $form_state)
    {
        $this->set_form_values($form_state);
    }
    public function set_form_values($form_state)
    {
        $this->api_key_value = $form_state->getValue("api_key");
        $this->congress_number_value = $form_state->getValue("congress_number");
    }
    public function find_existing_api_key()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_config where setting = 'pp_id' and value = '".$this->get_api_key_value()."' ;";
        $key = "value";
        $this->database->select_query($query, $key);
        if($this->database->get_result() != "error") {
            $this->insert_new_row();
        }
        else {

            }


    }
    public function update_existing_api_key(){

    }
    public function insert_new_row()
    {
        $table = "nfb_washington_config";
        $fields = array(
            'setting' => "pp_id",
            'value' => $this->get_api_key_value(),
            "active" => "0",
            "created_user" => \Drupal::currentUser()->getAccountName(),
            "last_modified_user" => \Drupal::currentUser()->getAccountName(),
        );
        $this->database = new base();
        $this->database->insert_query($table, $fields);
    }
}