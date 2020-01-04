<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\archive_nfb\activity_data;

class  meeting_backend extends base
{
    public function meeting_person(FormStateInterface $form_state)
    {
        $this->dependency_injection($form_state);
        $this->database_mapping($form_state, $params, $meeting_type);
        $this->archive_nfb = new activity_data();
        $this->district_data($form_state, $params);
        $this->archive_nfb->params_switch($form_state, $params);
        $this->set_email_meeting_body($form_state, $params, $meeting_type);
    }
    public function rating_backend(FormStateInterface $form_state)
    {
        $this->dependency_injection($form_state);
        $this->database_mapping($form_state, $params, $meeting_type);
        $this->district_data($form_state, $params);
        $this->archive_nfb = new activity_data();
        $this->archive_nfb->params_switch($form_state, $params);
        $this->set_ranking_email_body($form_state, $params);
    }
    public function database_mapping(FormStateInterface $form_state, &$params, &$meeting_type)
    {
        if($form_state->getFormObject()->getFormId() == 'washington_sem_new_meeting')
        {  $this->new_meeting_params($form_state, $params);
        $meeting_type = "new meeting has been scheduled";}
        elseif($form_state->getFormObject()->getFormId() == 'wash_sem_update_meeting')
        { $this->update_meeting($form_state, $params);
        $meeting_type = "meeting has been updated";}
        elseif($form_state->getFormObject()->getFormId() == "wash_sem_issue_rank")
        {$this->ranking_params_set_up($form_state, $params);
        $meeting_type = '';}
        else {die;}
    }
    public function new_meeting_database_map(FormStateInterface $form_state, &$params)
    {   $this->archive_nfb = new activity_data();
        $sem_id = $form_state->getValue('select_rep');
        $this->archive_nfb->find_rep_name($sem_id, $rep_name);
        $params['activity_name'] = "Meeting with ".$rep_name;
        $params['rep_name'] = $rep_name;
        $params['seminar_id'] = $form_state->getValue('select_rep');

        $params['location'] = $form_state->getValue('meeting_location');
    }
    public function update_meeting_database_map(FormStateInterface $form_state, &$params)
    {
        $params['meeting_id'] = $form_state->getValue('select_rep');
        \Drupal::logger('nfb_washington_debug')->notice($params['meeting_id']);
        $params['location'] = $form_state->getValue('meeting_location');
    }
    public function meeting_time_conversion(FormStateInterface $form_state, &$params)
    {
        $params['date'] = $form_state->getValue('meeting_day');
        $time = $form_state->getValue('meeting_time');
        if(strpos($time, 'PM') > 0)
        {   $hour = substr($time, 0, 2);
            if((int)$hour != 12)
            {$hour = (int)$hour + 12;
                $min = substr($time, 3, 2);}}
        elseif(substr($time, 0, 2) == '12')
        {$hour = 00;
            $min = substr($time, 3, 2);}
        elseif (strpos($time, 'AM') > 0)
        {    $hour = substr($time, 0, 2);
            if((int)$hour < 10){
            $hour = substr($time, 0, 1);
        $min = substr($time, 2,2);}
            else { $min = substr($time, 3,2);}}
        \Drupal::logger('nfb_washington')->notice($time);
        \Drupal::logger('nfb_washington')->notice($hour.$min);
        $params['time'] = $hour.$min;
    }
    public function district_data(FormStateInterface $form_state, &$params)
    {
       $string =  $form_state->getValue('select_rep');
       $params['district'] = substr($string,2,10);
    }
    public function contact_person(FormStateInterface $form_state, &$params)
    {
        $params['contact_name'] = $form_state->getValue('nfb_civicrm_f_name_1')." ".$form_state->getValue('nfb_civicrm_l_name_1');
        $params['contact_phone'] = $form_state->getValue('nfb_civicrm_phone_1');
    }
    public function user_and_meta_data(&$params)
    {
        $params['uid'] = \Drupal::currentUser()->id();
        $params['update_date'] = date('m/d/Y');
        $params['year'] = date('Y');
        $params['staff_lead'] = 'Kyle Walls';
        $params['staff_email'] = 'kwalls@nfb.org';
    }
    public function new_meeting_params(FormStateInterface $form_state, &$params)
    {
        $this->new_meeting_database_map($form_state, $params);
        $this->meeting_time_conversion($form_state, $params);
        $this->contact_person($form_state, $params);
        $this->user_and_meta_data($params);
    }
    public function update_meeting(FormStateInterface $form_state, &$params)
    {
        $this->update_meeting_database_map($form_state, $params);
        $this->meeting_time_conversion($form_state, $params);
        $this->meeting_time_conversion($form_state, $params);
        $this->contact_person($form_state, $params);
        $this->user_and_meta_data($params);
    }
    public function issue_switch(&$issue)
    {
        switch ($issue) {
            case 'Yes':
                $issue = 'y';
                break;
            case 'No':
                $issue = 'n';
                break;
            case 'Undecided':
                $issue = 'u';
                break;
            case 'Not Discussed':
                $issue = 'nd';
                break;
        }
    }
    public function set_issues(FormStateInterface $form_state, &$params)
    {
        $issue = $form_state->getValue('issue_1_ranking');
        $this->issue_switch($issue); $params['issue_1'] = $issue;
        $issue = $form_state->getValue('issue_2_ranking');
        $this->issue_switch($issue); $params['issue_2'] = $issue;
        $issue = $form_state->getValue('issue_3_ranking');
        $this->issue_switch($issue); $params['issue_3'] = $issue;
    }
    public function set_comments(FormStateInterface $form_state, &$params)
    {
        $params['comment_1'] = $form_state->getValue('issue_1_comment');
        $params['comment_2'] = $form_state->getValue('issue_2_comment');
        $params['comment_3'] = $form_state->getValue('issue_3_comment');
    }
    public function ranking_params_set_up(FormStateInterface $form_state, &$params)
    {
        $params['meeting_id'] = $form_state->getValue('select_rep');
        $this->contact_person($form_state, $params);
        $this->set_issues($form_state, $params);
        $this->set_comments($form_state, $params);
        $this->user_and_meta_data($params);
    }


}