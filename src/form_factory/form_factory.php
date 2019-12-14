<?php
Namespace Drupal\nfb_washington\form_factory;
class form_factory extends markup_elements
{
    public function build_new_meeting_time(&$form, $form_state)
    {
        $this->state_select_element($form,  $form_state);
        $this->contact_first_name_element($form, $form_state);
        $this->contact_last_name_element($form, $form_state);
        $this->contact_email_element($form, $form_state);
        $this->meeting_day_element($form, $form_state);
        $this->meeting_time_element($form, $form_state);
        $this->meeting_comments_element($form, $form_state);
    }
    public function build_update_meeting_form(&$form, $form_state)
    {
        $this->state_select_element($form, $form_state);
        $this->contact_first_name_element($form, $form_state);
        $this->contact_last_name_element($form, $form_state);
        $this->contact_email_element($form, $form_state);
        $this->meeting_day_element($form, $form_state);
        $this->meeting_time_element($form, $form_state);
        $this->meeting_comments_element($form, $form_state);
    }
    public function build_rating_form(&$form, $form_state)
    {
        $this->state_select_element($form, $form_state);
        $this->contact_first_name_element($form, $form_state);
        $this->contact_last_name_element($form, $form_state);
        $this->contact_email_element($form, $form_state);
        $this->issue_1_ranking_element($form, $form_state);
        $this->issue_1_comment_element($form, $form_state);
        $this->issue_2_ranking_element($form, $form_state);
        $this->issue_2_comment_element($form, $form_state);
        $this->issue_3_ranking_element($form, $form_state);
        $this->issue_3_comment_element($form, $form_state);
    }
    public function build_home_page_form(&$form, $form_state){
        $this->state_select_element($form, $form_state);
        $this->build_meeting_info_button($form, $form_state);
    }
    
}