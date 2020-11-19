<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\nfb_washington\database\base;

class admin_com_member
{
    public $database;
    public function  build_form_array(&$form, $form_state)
    {
        $this->initial_markup($form, $form_state);
        $this->select_committee($form, $form_state);
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",
        );

    }
    public function initial_markup(&$form, $form_state)
    {
        $form['intro_markup'] = array(
          '#type' => "item",
          "#markup" => "<p>Maintain the committee member records</p>"
        );
    }
    public function select_committee(&$form, $form_state)
    {
        $form['committee_value'] = array(
          '#type' => 'select',
          '#required' => 'true',
          "#options"  => $this->select_options(),
          '#title'  => "Select Committee"
        );
    }
    public function select_options()
    {
        $options = [];
        $this->database = new base();
        $query = "select * from nfb_washington_committee;";
        $key = "committee_id";
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $option)
        {
            $option = get_object_vars($option);
            $options[$option['committee_id']] = $option['propublica_id'].": ".$option['committee_name'];
        }
        $this->database = null;
        return $options;
    }
}