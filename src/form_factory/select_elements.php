<?php
namespace Drupal\nfb_washington\form_factory;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_civicrm_bridge\civicrm\query;
use Drupal\nfb_washington\archive_nfb\representative_data;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\database\base;

class select_elements extends textfield_elements
{
    protected $civicrm;
    public $database;
    public $options;
    public function get_element_options()
    {return $this->options;}
    public function build_Static_select_box(&$form, $form_state)
    {   $form[$this->get_element_id()] = array(
        '#type' => $this->get_element_type(),
        '#title' => $this->t($this->get_element_title()), // do not stack translation trait elements
        '#options' => $this->get_element_options(),
        '#required' => $this->get_element_required_status(),);}
    public function build_ajax_select_box(&$form, $form_state)
    {
        $form[$this->get_element_id()] = array(
            '#type' => $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()), // do not stack translation trait elements
            '#options' => $this->get_element_options(),
            '#required' => $this->get_element_required_status(),
            '#ajax' => array(
                'callback' => $this->get_callback(),
                'wrapper' => $this->get_wrapper(),
                'event' => 'change',
            ),
        );
    }
    public function build_ajax_wrapped_select(&$form, $form_state, $options)
    {
        $form[$this->get_element_id()] = array(
            '#prefix' => $this->get_prefix(),
            '#type' => $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()), // do not stack translation trait elements
            '#options' => $options,
            '#required' => $this->get_element_required_status(),
            '#suffix' => $this->get_suffix(),
        );
    }
    public function build_wrapped_ajax_select(&$form, $form_state, $options)
    {
        $form[$this->get_element_id()] = array(
            '#prefix' => $this->get_prefix(),
            '#type' => $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()), // do not stack translation trait elements
            '#options' => $options,
            '#required' => $this->get_element_required_status(),
            '#suffix' => $this->get_suffix(),
        );
    }
    public function rankings_options()
    {
        $this->options = array(
          'Yes' => $this->t('Yes'),
          'No' => $this->t('No'),
          'Undecided' => $this->t('Undecided'),
          'Not Discussed' => $this->t('Not Discussed'),
        );
    }
    public function issue_1_ranking_element(&$form, $form_state)
    {
        $this->rankings_options();
        $this->element_id = 'issue_1_ranking';
        $this->type = 'select';
        $this->representative_data =  new representative_data();
        $rank = 1;
        $this->representative_data->get_issue_name($rank, $issue, $id);
        $this->title = $issue." Rating";
        $this->required = True;
        $this->build_Static_select_box($form, $form_state);
    }
    public function issue_2_ranking_element(&$form, $form_state)
    {
        $this->rankings_options();
        $this->element_id = 'issue_2_ranking';
        $this->type = 'select';
        $this->representative_data =  new representative_data();
        $rank = 2;
        $this->representative_data->get_issue_name($rank, $issue, $id);
        $this->title = $issue." Rating";
        $this->required = True;
        $this->build_Static_select_box($form, $form_state);
    }
    public function issue_3_ranking_element(&$form, $form_state)
    {
        $this->rankings_options();
        $this->element_id = 'issue_3_ranking';
        $this->type = 'select';
        $this->representative_data =  new representative_data();
        $rank = 3;
        $this->representative_data->get_issue_name($rank, $issue, $id);
        $this->title = $issue." Rating";
        $this->required = True;
        $this->build_Static_select_box($form, $form_state);
    }
    public function moc_select_element(&$form)
    { //todo implement this code once connection to archive.nfb.org is established
        }
    public function meeting_time_element(&$form, $form_state)
    {
        $this->time_options(); $this->type = 'select';
        $this->title = "Meeting Time"; $this->required = True;
        $this->element_id = "meeting_time"; $this->build_Static_select_box($form, $form_state);

    }
    public function state_select_element(&$form,$form_state)
    {
        $this->state_options(); $this->type = 'select';
        $this->title = "Select State"; $this->required = TRUE;
        $this->element_id = 'select_state'; $this->build_Static_select_box($form,  $form_state);
    }
    public function state_ajax_select_element(&$form,$form_state)
    {
        $this->state_options(); $this->type = 'select';
        $this->title = "Select State"; $this->required = TRUE;
        $this->element_id = 'select_state'; $this->callback = '::staterep_refresh';
        $this->wrapper = 'rep_wrapper';
        $this->build_ajax_select_box($form,  $form_state);

    }
    public function state_rep_ajax_select_element(&$form, $form_state){
        $this->new_drupal_rep_options( $form_state, $options);
        $this->options = $options; $this->prefix = "<div id='rep_wrapper'>";
        $this->element_id = 'select_rep'; $this->type = 'select';
        $this->title = "Select Elected Official"; $this->required = TRUE;
        $this->suffix = "</div>";
        $this->build_ajax_wrapped_select($form, $form_state, $options);
    }
    public function update_state_rep_meeting_select_elements(&$form, $form_state)
    {   $this->representative_data = new representative_data();
        $this->representative_data->create_update_meeting_options($form_state, $options);
        $this->representative_data = null;
        $this->options = $options; $this->prefix = "<div id='rep_wrapper'>";
        $this->element_id = 'select_rep'; $this->type = 'select';
        $this->title = "Select Elected Official"; $this->required = TRUE;
        $this->suffix = "</div>"; $this->callback = '::data_refresh';
        $this->wrapper = 'data_wrapper';
        $this->build_wrapped_ajax_select($form, $form_state, $options);
    }
    public function new_ranking_select_element(&$form, $form_state)
    {   $this->representative_data = new representative_data();
        $this->representative_data->create_new_rating_options($form_state, $options);
        $this->representative_data = null;
        $this->options = $options; $this->prefix = "<div id='rep_wrapper'>";
        $this->element_id = 'select_rep'; $this->type = 'select';
        $this->title = "Select Elected Official"; $this->required = TRUE;
        $this->suffix = "</div>"; $this->callback = '::data_refresh';
        $this->wrapper = 'data_wrapper';
        $this->build_wrapped_ajax_select($form, $form_state, $options);
    }
    public function update_ranking_select_element(&$form, $form_state)
    {   $this->representative_data = new representative_data();
        $this->representative_data->create_update_rating_options($form_state, $options);
        $this->representative_data = null;
        $this->options = $options; $this->prefix = "<div id='rep_wrapper'>";
        $this->element_id = 'select_rep'; $this->type = 'select';
        $this->title = "Select Elected Official"; $this->required = TRUE;
        $this->suffix = "</div>"; $this->callback = '::data_refresh';
        $this->wrapper = 'data_wrapper';
        $this->build_wrapped_ajax_select($form, $form_state, $options);
    }
    public function time_options()
    {
        $this->am_options($options);
    $this->pm_options($options);
    $this->options = $options;
    }
    public function state_options()
    {
        $this->set_up_civi($result);
        $this->set_state_options($result, $options);
        $this->options = $options;
    }
    public function set_up_civi(&$result)
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civicrm = new query($civi);
        $this->civicrm->mode = 'get'; $this->civicrm->entity = 'StateProvince';
        $this->civicrm->params = array(
            'sequential' => 1,
            'country_id' => "1228",
            'options' => ['limit' => 60],
        );
        $this->civicrm->civi_query($result);
    }
    public function new_attendance_element(&$form, $form_state)
    {
        $form['attendance'] = array(
            '#type' => 'select',
            '#title' => "Is this member attending the meeting?",
            '#options' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            '#required' => TRUE,
        );
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
    public function am_options(&$options)
    {
        $options['12:00 AM'] = "12:00 AM";
        $options["12:15 AM"] = "12:15 AM";
        $options["12:30 AM"] = "12:30 AM";
        $options["12:45 AM"] = "12:45 AM";
        $options["1:00 AM"] =  "1:00 AM";
        $options["1:15 AM"] = "1:15 AM";
        $options["1:30 AM"] = "1:30 AM";
        $options["1:45 AM"] = "1:45 AM";
        $options["2:00 AM"] =  "2:00 AM";
        $options["2:15 AM"] = "2:15 AM";
        $options["2:30 AM"] = "2:30 AM";
        $options["2:45 AM"] = "2:45 AM";
        $options["3:00 AM"] =  "3:00 AM";
        $options["3:15 AM"] = "3:15 AM";
        $options["3:30 AM"] = "3:30 AM";
        $options["3:45 AM"] = "3:45 AM";
        $options["4:00 AM"] =  "4:00 AM";
        $options["4:15 AM"] = "4:15 AM";
        $options["4:30 AM"] = "4:30 AM";
        $options["4:45 AM"] = "4:45 AM";
        $options["5:00 AM"] =  "5:00 AM";
        $options["5:15 AM"] = "5:15 AM";
        $options["5:30 AM"] = "5:30 AM";
        $options["5:45 AM"] = "5:45 AM";
        $options["6:00 AM"] =  "6:00 AM";
        $options["6:15 AM"] = "6:15 AM";
        $options["6:30 AM"] = "6:30 AM";
        $options["6:45 AM"] = "6:45 AM";
        $options["7:00 AM"] =  "7:00 AM";
        $options["7:15 AM"] = "7:15 AM";
        $options["7:30 AM"] = "7:30 AM";
        $options["7:45 AM"] = "7:45 AM";
        $options["8:00 AM"] =  "8:00 AM";
        $options["8:15 AM"] = "8:15 AM";
        $options["8:30 AM"] = "8:30 AM";
        $options["8:45 AM"] = "8:45 AM";
        $options["9:00 AM"] =  "9:00 AM";
        $options["9:15 AM"] = "9:15 AM";
        $options["9:30 AM"] = "9:30 AM";
        $options["9:45 AM"] = "9:45 AM";
        $options["10:00 AM"] =  "10:00 AM";
        $options["10:15 AM"] = "10:15 AM";
        $options["10:30 AM"] = "10:30 AM";
        $options["10:45 AM"] = "10:45 AM";
        $options["11:00 AM"] =  "11:00 AM";
        $options["11:15 AM"] = "11:15 AM";
        $options["11:30 AM"] = "11:30 AM";
        $options["11:45 AM"] = "11:45 AM";
    }
    public function pm_options(&$options)
    {
        $options['12:00 PM'] = "12:00 PM";
        $options["12:15 PM"] = "12:15 PM";
        $options["12:30 PM"] = "12:30 PM";
        $options["12:45 PM"] = "12:45 PM";
        $options["1:00 PM"] =  "1:00 PM";
        $options["1:15 PM"] = "1:15 PM";
        $options["1:30 PM"] = "1:30 PM";
        $options["1:45 PM"] = "1:45 PM";
        $options["2:00 PM"] =  "2:00 PM";
        $options["2:15 PM"] = "2:15 PM";
        $options["2:30 PM"] = "2:30 PM";
        $options["2:45 PM"] = "2:45 PM";
        $options["3:00 PM"] =  "3:00 PM";
        $options["3:15 PM"] = "3:15 PM";
        $options["3:30 PM"] = "3:30 PM";
        $options["3:45 PM"] = "3:45 PM";
        $options["4:00 PM"] =  "4:00 PM";
        $options["4:15 PM"] = "4:15 PM";
        $options["4:30 PM"] = "4:30 PM";
        $options["4:45 PM"] = "4:45 PM";
        $options["5:00 PM"] =  "5:00 PM";
        $options["5:15 PM"] = "5:15 PM";
        $options["5:30 PM"] = "5:30 PM";
        $options["5:45 PM"] = "5:45 PM";
        $options["6:00 PM"] =  "6:00 PM";
        $options["6:15 PM"] = "6:15 PM";
        $options["6:30 PM"] = "6:30 PM";
        $options["6:45 PM"] = "6:45 PM";
        $options["7:00 PM"] =  "7:00 PM";
        $options["7:15 PM"] = "7:15 PM";
        $options["7:30 PM"] = "7:30 PM";
        $options["7:45 PM"] = "7:45 PM";
        $options["8:00 PM"] =  "8:00 PM";
        $options["8:15 PM"] = "8:15 PM";
        $options["8:30 PM"] = "8:30 PM";
        $options["8:45 PM"] = "8:45 PM";
        $options["9:00 PM"] =  "9:00 PM";
        $options["9:15 PM"] = "9:15 PM";
        $options["9:30 PM"] = "9:30 PM";
        $options["9:45 PM"] = "9:45 PM";
        $options["10:00 PM"] =  "10:00 PM";
        $options["10:15 PM"] = "10:15 PM";
        $options["10:30 PM"] = "10:30 PM";
        $options["10:45 PM"] = "10:45 PM";
        $options["11:00 PM"] =  "11:00 PM";
        $options["11:15 PM"] = "11:15 PM";
        $options["11:30 PM"] = "11:30 PM";
        $options["11:45 PM"] = "11:45 PM";
    }
    public function new_drupal_rep_options(FormStateInterface $form_state, &$options)
    {
        If($form_state->getValue("select_state") != ""){
        $this->database = new base();
            $query = "select * from nfb_washington_members where state = '".$form_state->getValue("select_state")."'
        and active = 0 and district = 'Senate';";
            $key = 'member_id';
            $this->database->select_query($query, $key);
            $member_result = $this->database->get_result();
            $options = [];
            $this->options_member_loop($member_result, $options);
            $this->database = new base();
            $query = "select * from nfb_washington_members where state = '".$form_state->getValue("select_state")."'
        and active = 0 and district != 'Senate';";
            $key = 'member_id';
            $this->database->select_query($query, $key);
            $member_result = $this->database->get_result();
            $this->options_member_loop($member_result, $options);
            $this->database = null;
        }
        else {$options = [];}
    }
    public function options_member_loop($member_result, &$options)
    {
        foreach ($member_result as  $member)
        {
            $member = get_object_vars($member);
            $this->build_member_array($member, $member_array);
            $this->civicrm_member_names($member, $member_array);
            if($member_array['district'] = "Senate")
            {$option_text = "Senator ".$member_array['first_name']." ".$member_array['last_name'];}
            else {$option_text = $member_array['first_name']." ".$member_array['last_name']. " ".$member_array['state']. " ".$member_array['district'];}
        $options[$member_array['id']] = $option_text;
        }
    }
    public function build_member_array($member, &$member_array)
    {
        $member_array['id'] = $member['member_id'];
        $member_array['state'] = $member['state'];
        $member_array['district'] = $member['district'];
        $member_array['rank'] = $member['rank'];
    }
    public function civicrm_member_names($member, &$member_array)
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civicrm = new civi_query($civi);
        $this->civicrm->civi_mode = 'get';
        $this->civicrm->civi_entity = 'Contact';
        $this->civicrm->civi_params = array(
            'sequential' => 1,
            'id' => $member['civicrm_contact_id'],
        ); $first_name = null; $last_name = null;
        $this->civicrm->civi_query();
        foreach($this->civicrm->get_civicrm_result()['values'] as $result)
        {
            if($first_name == null)
            {$first_name = $result['first_name'];}
            if($last_name == null)
            {$last_name = $result['last_name'];}
        }
        $member_array['first_name'] = $first_name;
        $member_array['last_name'] = $last_name;
        $this->civicrm = null;
    }


}