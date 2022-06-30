<?php
Namespace Drupal\nfb_washington\form_factory;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\civicrm\Civicrm;
class form_factory extends update_form_ajax_test
{
    public $civi_query_v4;
    public function build_new_meeting_time(&$form, $form_state, $meeting)
    {
        if($meeting == "new"){
        $this->state_ajax_select_element($form,  $form_state);
        $this->state_rep_ajax_select_element($form, $form_state);}
        elseif(strpos(" ".$meeting, "new") > 0 && strlen($meeting) > 3)
        {
            $this->build_directlink_select($form, $form_state, $meeting);
        }
        $this->meeting_hidden_value($form, $form_state, $meeting);

        if($meeting == "new") {
        $this->contact_first_name_element($form, $form_state);
        $this->contact_last_name_element($form, $form_state);}
        elseif(strpos(" ".$meeting, "new") > 0 && strlen($meeting) > 3)
        {
            $this->contact_first_name_element($form, $form_state);
            $this->contact_last_name_element($form, $form_state);
        }
        else { $this->update_first_name($form, $form_state);}
        $this->contact_email_element($form, $form_state);
        $this->meeting_day_element($form, $form_state);
        $this->meeting_time_element($form, $form_state);
        $this->meeting_comments_element($form, $form_state);
        $this->new_attendance_element($form, $form_state);
        $this->MOC_contact_element($form, $form_state);
        if($meeting == "new"){
        $this->submit_button($form, $form_state);}
        else{
            $this->build_confirmation_checkbox($form, $form_state);
            $this->conditional_submit($form, $form_state);
        }
    }

    public function build_update_rating_form(&$form, $form_state, $rating)
    {
        $this->set_issue_limit();
        if($rating == "new"){
            $this->state_ajax_select_element($form,  $form_state);
            $this->state_rep_ajax_select_element($form, $form_state);
        }
        elseif (strlen($rating ) > 3 && substr($rating, 0 ,3) == "new")
        {
            $meeting = $rating;
            $this->build_directlink_select($form, $form_state, $meeting);
        }
        $this->raiting_hidden_value($form, $form_state, $rating);
        $this->update_first_name($form, $form_state);
        $this->contact_email_element($form, $form_state);
        $this->issue_1_ranking_element($form, $form_state);
        $this->issue_1_comment_element($form, $form_state);
        if($this->get_issue_count() > 1){
        $this->issue_2_ranking_element($form, $form_state);
        $this->issue_2_comment_element($form, $form_state);}
        if($this->get_issue_count() > 2){
        $this->issue_3_ranking_element($form, $form_state);
        $this->issue_3_comment_element($form, $form_state);}
        if($this->get_issue_count() > 3) {
            $this->issue_4_ranking_element($form, $form_state);
            $this->issue_4_comment_element($form,$form_state);}
        if($this->get_issue_count() > 4) {
            $this->issue_5_ranking_element($form, $form_state);
            $this->issue_5_comment_element($form, $form_state);}
        if($rating == 'new')
        {$this->submit_button($form, $form_state);}
        else{
            $this->build_confirmation_checkbox($form, $form_state);
            $this->conditional_submit($form, $form_state);
        }
    }
    public function build_home_page_form(&$form, $form_state){
        $this->state_select_element($form, $form_state);
        $this->build_meeting_info_button($form, $form_state);
        $this->build_meeting_info_markup($form, $form_state);
    }
    public function build_confirmation_checkbox(&$form, $form_state)
    {
        $form['confirm'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('I confirm I want to make this update'),
            '#required' => 'true',
        );
    }
    public function conditional_submit(&$form, $form_state)
    {
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#states' => [
                'visible' =>[
                    [':input[name="confirm"]' => ['checked' => true]]],],
        );
    }
    public function meeting_hidden_value(&$form, $form_state, $meeting)
    {
        $form['meeting_value'] = array(
            '#type' => 'textfield',
            '#value' => $meeting,
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Drupal meeting Id"
        );
    }
    public function raiting_hidden_value(&$form, $form_state, $rating)
    {
        $form['rating_value'] = array(
            '#type' => 'textfield',
            '#value' => $rating,
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Drupal meeting Id"
        );
    }
    public function set_issue_limit()
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
    public function direct_link_query($member_id)
    {
        $query = "select * from nfb_washington_members where member_id = '".$member_id."' and active = '0';";
        $key = 'member_id';
        $this->database = new base();
        $this->database->select_query($query, $key);
        $data = [];
        if($this->database->get_result() != "error"|| $this->database->get_result() != array())
        {
            foreach($this->database->get_result() as $member)
            {
                $result = get_object_vars($member);
                $data['civi_id'] = $result['civicrm_contact_id'];
                $data['state'] = $result['state'];
            }
        }
        return $data;
    }
    public function get_first_name_last_name_direct($meeting, &$data)
    {
        $member_id = substr($meeting, 3, 10);
        $data = $this->direct_link_query($member_id);
        $civicrm = new Civicrm();
        $this->civi_query_v4 = new civicrm_v4($civicrm);
        $this->civi_query_v4->civi_mode = "get";
        $this->civi_query_v4->civi_entity = "Contact";
        $this->civi_query_v4->civi_params = array(
            'select' => [
                'first_name',
                'last_name',
            ],
            'where' => [
                ['id', '=', $data['civi_id']],
            ],
            'limit' => 1,
        );
        $result = $this->civi_query_v4->civi_query_v4();
        foreach ( $result as $value) {
            $data['first_name'] = $value['first_name'];
            $data['last_name'] = $value['last_name'];
        }
        $data['member_id'] = $member_id;
    }
    public function build_directlink_select(&$form, $form_state, $meeting)
    {
        $this->get_first_name_last_name_direct($meeting, $data);
        $options = [$data['member_id'] => $data['first_name']." ".$data['last_name']]; $this->prefix = "<div id='rep_wrapper'>";
        $this->element_id = 'select_rep'; $this->type = 'select';
        $this->title = "Select Elected Official"; $this->required = TRUE;
        $this->suffix = "</div>";
        $this->build_ajax_wrapped_select($form, $form_state, $options);
        $form['select_rep']['#value'] = $data['member_id'];
    }




}