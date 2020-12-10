<?php
Namespace Drupal\nfb_washington\form_factory;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\database\base;

class markup_elements extends date_elements
{
    public $markup;
    public $database;
    public $civicrm;
    public function get_markup()
    {return $this->markup;}

    public function build_static_markup(&$form, $form_state)
    {
        $form[$this->get_element_id()] = array(
            '#type' => $this->get_element_type(),
            '#markup' => $this->get_markup(),);
    }
    public function build_ajax_markup(&$form, $form_state)
    {
        $form[$this->get_element_id()] = array(
            '#prefix' => $this->get_prefix(),
            '#type' => $this->get_element_type(),
            '#markup' => $this->get_markup(),
            '#suffix' => $this->get_suffix(),);
    }
    public function build_button_element(&$form, $form_State)
    {
        $form[$this->get_element_id()] = array(
          '#type' => $this->get_element_type(),
          '#value' => $this->t($this->get_element_title()),
          '#ajax' => array(
              'callback' => $this->get_callback(),
              'wrapper' => $this->get_wrapper(),
              'event' => 'click',),
        );
    }
    public function build_meeting_info_button(&$form, $form_state)
    {
        $this->type = 'button'; $this->title = "Find Meetings";
        $this->element_id = 'meeting_button';
        $this->callback = '::refresh_meeting'; $this->wrapper = 'meeting_markup';
        $this->event = 'click'; $this->build_button_element($form, $form_state);
    }
    public function build_meeting_info_markup(&$form, $form_state)
    {
        $this->type = "item"; $this->markup = $this->temp_ajax_test($form, $form_state);
        $this->element_id = 'meeting_info_markup';
        $this->prefix = "<div id = meeting_markup>"; $this->suffix = "</div>";
        $this->build_ajax_markup($form, $form_state);
    }
    public function temp_ajax_test(&$form, $form_state)
    {
        if($form_state->getValue('select_state') != '')
        {$this->new_home_markup($form_state, $markup);}
        else {return "<p>Please Select a state</p>";}
        return $markup;
    }
    public function submit_button(&$form, $form_state)
    {
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),);
    }
    public function new_home_markup(FormStateInterface $form_state, &$markup)
    {
        $markup = "<table>
    <tr><th class='table-header'>Member of Congress<th class='table-header'>Chamber</th><th class='table-header'>District/Rank</th><th class='table-header'>Meeting Location</th><th class='table-header'>Meeting Time</th><th class='table-header'>NFB Contact</th><th class='table-header'>Member of Congress Contact Person</th><th class='table-header'>Details/Meeting/Rating</th></tr>";
        $this->database = new base();
        $query = "select * from nfb_washington_members where state = '".$form_state->getValue("select_state")."'
        and active = 0 and district = 'Senate';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $member_result = $this->database->get_result();
        $this->member_loop($member_result, $markup);
        $this->database = new base();
        $query = "select * from nfb_washington_members where state = '".$form_state->getValue("select_state")."'
        and active = 0 and district != 'Senate';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $member_result = $this->database->get_result();
        $this->member_loop($member_result, $markup);
        $markup = $markup."</table>";
    }
    public function member_loop($member_result, &$markup)
    {
        foreach ($member_result as $member)
        {
            $member = get_object_vars($member);
            $this->set_member_values($member, $member_array);
            $this->get_first_nad_last_name($member, $member_array);
            $this->find_meeting($member, $member_array);
            $this->table_row($member_array, $markup );
        }
    }
    public function set_member_values($member, &$member_array)
    {
        $member_array['id'] = $member['member_id'];
        $member_array['civi_id'] = $member['civicrm_contact_id'];
        $member_array['propublica_id'] = $member['propublica_id'];
        $member_array['state'] = $member['state'];
        $member_array['district'] = $member['district'];
        $member_array['rank'] = $member['rank'];
    }
    public function get_first_nad_last_name($member, &$member_array)
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
    public function find_meeting($member, &$member_array)
    {
        $this->database = new base(); $year = date('Y');
        $query = "select * from nfb_washington_activities where meeting_year = '".$year."'
        and member_id = '".$member['member_id']."' and type  = 'meeting';";
        $key = 'activity_id';
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $activity)
        {
            $activity = get_object_vars($activity);
            $date = $activity['meeting_date'];
            $time = $activity['meeting_time'];
            $description = $activity['description'];
            $location = $activity['location'];
            $moc_contact = $activity['m_o_c_contact'];
            $nfb_contact = $activity['nfb_contact'];
            $nfb_phone = $activity['nfb_phone'];
            $activity_id = $activity['activity_id'];
        }
        if($activity_id == null || $activity_id == false || $activity_id == "") {
            $member_array['meeting_time'] = "N/A";
            $member_array['meeting_date'] = "N/A";
            $member_array['subject'] = "N/A";
            $member_array['moc_contact'] = "N/A";
            $member_array['nfb_contact'] = "N/A";
            $member_array['location'] = "N/A";
            $member_array['date'] = "N/A";
            $member_array['time'] = "";
            $member_array['meeting_id'] = "new";
            $member_array['rating_status'] = 'new';
        }
        else{
            $member_array['meeting_time'] = $time;
            $member_array['meeting_date'] = $date;
            $member_array['subject'] = $description;
            $member_array['moc_contact'] = $moc_contact;
            $member_array['nfb_contact'] = $nfb_contact." Phone #:".$nfb_phone;
            $member_array['location'] = $location;
            $member_array['meeting_id'] = $activity_id;
        }
        if($member_array['meeting_id'] != "new")
        {

        }
        $this->database = null;
    }
    public function  find_ratings(&$member_array)
    {
        $this->database = new base();
        $rating_id = null;
        $query = "select * from nfb_washington_rating where activity_id '".$member_array['meeting_id']."';";
        $key = 'rating_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $rating)
        {
            $rating = get_object_vars($rating);
            if($rating_id == null)
            {$rating_id = $rating['rating_id'];}
        }
        if($rating_id == null)
        {$member_array['rating_status'] = "new";}
        else {$member_array['rating_status'] = "edit";}
    }
    public function table_row($member_array, &$markup)
    {
        if($member_array['district'] == "Senate")
        {
            $chamber = "Senate";
        }
        else {$chamber = "House";}
        if($chamber == "Senate")
        {
            $rank = $member_array['state']." ".$member_array['rank'];
        }
        else {
            $rank = $member_array['state']." ".$member_array['district'];
        }
        if($member_array['rating_status'] == "new")
        {$button_3 = "Rate"; $button_3_url = "new";} else {$button_3 = "Edit";
        $button_3_url = $member_array['meeting_id'];}
        if($member_array['meeting_id'] == "new") {$button_2 = "Schedule";}
        else{$button_2 = "Edit";}
            $markup = $markup."<tr><td>".$member_array['first_name']." ".$member_array['last_name']."</td><td>".$chamber."</td><td>$rank</td><td>".$member_array['location']."</td><td>".$member_array['meeting_date']." ".$member_array['meeting_time']."</td><td>".$member_array['nfb_contact']."</td><td>".$member_array['moc_contact']."</td><td><a href='/nfb-washington/meeting/".$member_array['meeting_id']."' class='button-2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$button_2."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;<a a href='/nfb-washington/rating/".$button_3_url."' class='button-3' role = 'button'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$button_3."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></td></tr>";
    }




}