<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\archive_nfb\activity_data;
class  meeting_backend extends base
{
    public function meeting_person(FormStateInterface $form_state)
    {
        $this->dependency_injection($form_state);
        $this->database_mapping($form_state, $params);
    }
    public function database_mapping(FormStateInterface $form_state, &$params)
    {
        if($form_state->getFormObject()->getFormId() == 'washington_sem_new_meeting')
        {  $this->new_meeting_params($form_state, $params);}
        elseif($form_state->getFormObject()->getFormId() == 'wash_sem_update_meeting')
        { $this->update_meeting($form_state, $params);}
        elseif($form_state->getFormObject()->getFormId() == "wash_sem_issue_rank")
        {}
        else { // todo implement error catch
            }
    }
    public function new_meeting_database_map(FormStateInterface $form_state, &$params)
    {   $this->archive_nfb = new activity_data();
        $sem_id = $form_state->getValue('select_rep');
        $this->archive_nfb->find_rep_name($sem_id, $rep_name);
        $params['activity_name'] = "Meeting with ".$rep_name;
        $params['seminar_id'] = $form_state->getValue('select_rep');
        $params['location'] = $form_state->getValue('meeting_location');
    }
    public function update_meeting_database_map(FormStateInterface $form_state, &$params)
    {
        $params['meeting_id'] = $form_state->getValue('select_rep');
        $params['location'] = $form_state->getValue('meeting_location');
    }
    public function meeting_time_conversion(FormStateInterface $form_state, &$params)
    {
        $params['date'] = $form_state->getValue('meeting_day');
        $time = $form_state->getValue('meeting_time');
        if(strpos($time, 'PM') > 0)
        {   $hour = substr($time, 0, 2);
            if((int)$hour != 12)
            {$hour = (int)$hour + 12;}}
        elseif(substr($time, 0, 2) == '12')
        {$hour = 00;}
        $min = substr($time, 3, 2);
        $params['time'] = $hour.$min;
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
    public function update_meeting(FormStateInterface $form_state, $params)
    {
        $this->update_meeting($form_state, $params);
        $this->meeting_time_conversion($form_state, $params);
        $this->contact_person($form_state, $params);
        $this->user_and_meta_data($params);
    }

}