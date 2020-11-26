<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_civicrm_bridge\civicrm\query;
use Drupal\nfb_washington\database\base;

class admin_note_link
{
    public $database;
    public $civicrm;
    public function build_form_array(&$form, FormStateInterface $form_state)
    {

    }
    public function state_select( &$form, $form_state)
    {
        $form['member_state'] = array(
          '#type' => "select",
          '#title' => "Select Member State",
          '#required' => true,
          '#options' => $this->set_up_state_options(),
          '#ajax' => array(
             'event' => 'change',
              "callback" => '::member_refresh',
              'wrapper' => "member_select"
          ) ,
        );
    }
    public function set_up_state_options(){
        $civi = new Civicrm(); $civi->initialize();
        $this->civicrm = new query($civi);
        $this->civicrm->mode = 'get'; $this->civicrm->entity = 'StateProvince';
        $this->civicrm->params = array(
            'sequential' => 1,
            'country_id' => "1228",
            'options' => ['limit' => 60],
        );
        $this->civicrm->civi_query($result);
        $this->set_state_options($result, $options);
        $this->civicrm = null;
        return $options;
    }
    public function set_state_options($result, &$options)
    {
        foreach($result['values'] as $state)
        {
            if($state['id'] != "1052" && $state['id'] != "1053" &&$state['id'] != "1055"
                && $state['id'] != "1057" && $state['id'] != "1058" && $state['id'] != "1059"
                && $state['id'] != "1060" && $state['id'] != "1061"){
                $options[$state['abbreviation']] = $state['name'];}
        }
        ksort($options);
    }
    public function member_options(&$form, &$form_state){
       $form['member'] = array(
         '#preffix' => "<div id = 'member_select'>",
         '#type' => 'select',
         '#title' => "Select Member of Congress",
         '#required'  =>  true,
          '#options'  => ''
       );

    }
    public function member_option_create(FormStateInterface $form_state)
    {
        if($form_state->getValue("member_sate") == '')
        {$options = [];}
        else {}
        return $options;
    }
    public function get_member_ids_query(FormStateInterface $form_state)
    {
        $this->database = new base();
        $query  = "select * from nfb_washington_members where active = 0 and 
  state = '".$form_state->getValue("member_state")."';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $query_result = $this->database->get_result();
        $this->database = null;
    }
    public function member_lopp($query_result, &$options)
    {

    }
}