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
        $this->state_select($form, $form_state);
        $this->member_options($form, $form_state);
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",
        );
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
          '#options'  => $this->member_option_create($form_state),
           '#suffix' => '</div>'
       );

    }
    public function member_option_create(FormStateInterface $form_state)
    {
        if($form_state->getValue("member_state") == '')
        {$options = [];}
        else {$this->member_selecct_options($form_state, $options);}
        return $options;
    }
    public function member_selecct_options($form_state, &$options)
    {
        $this->get_member_ids_query($form_state, $options);

    }
    public function get_member_ids_query(FormStateInterface $form_state, &$options)
    {
        $this->database = new base();
        $query  = "select * from nfb_washington_members where active = 0 and 
  state = '".$form_state->getValue("member_state")."';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $query_result = $this->database->get_result();

        $this->database = null;
        $this->member_loop($query_result, $options);
    }
    public function member_loop($query_result, &$options)
    {
        foreach ($query_result as $member)
        {
            $member = get_object_vars($member);
            $option_array['id'] = $member['member_id'];
            $option_array['civicrm_id'] = $member['civicrm_contact_id'];
            $this->find_member_name($option_array);
            $options[$option_array['id']] = $option_array['first_name']." ".$option_array['last_name'];
        }
        \Drupal::logger("nfb_wasington_ajax_options_debug")->logger("options_array: ".print_r($options, true));
    }
    public function find_member_name(&$option_array)
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civicrm = new query($civi);
        $this->civicrm->entity = 'Contact';
        $this->civicrm->mode = 'get';
        $this->civicrm->params = array(
            'sequential' => 1,
            'id' =>  $option_array['civicrm_id'],
        );
        $this->civicrm->civi_query($result);
        $option_array['first_name'] = null;
        $option_array['last_name'] = null;
        foreach ($result['values'] as $member)
        {
            if($option_array['first_name'] == null)
            {$option_array['first_name'] = $member['first_name'];}
            if($option_array['last_name'] == null)
            {$option_array['last_name'] = $member['last_name'];}
        }
        $this->civicrm = null;
        \Drupal::logger("nfb_wasington_ajax_debug")->logger("options_array: ".print_r($option_array, true));
    }
}
