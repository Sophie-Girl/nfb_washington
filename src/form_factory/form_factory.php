<?php
Namespace Drupal\nfb_washington\form_factory;
class form_factory extends markup_elements
{
    public function build_new_meeting_time(&$form)
    {
        $this->state_select_element($form);
        $this->contact_first_name_element($form);
        $this->contact_last_name_element($form);
        $this->contact_email_element($form);
        $this->meeting_day_element($form);
        $this->meeting_time_element($form);
        $this->meeting_comments_element($form);
    }
    public function build_update_meeting_form(&$form)
    {
        $this->state_select_element($form);
        $this->contact_first_name_element($form);
        $this->contact_last_name_element($form);
        $this->contact_email_element($form);
        $this->meeting_day_element($form);
        $this->meeting_time_element($form);
        $this->meeting_comments_element($form);
    }
    public function build_rating_form(&$form)
    {
        $this->state_select_element($form);
        $this->contact_first_name_element($form);
        $this->contact_last_name_element($form);
        $this->contact_email_element($form);
        $this->issue_1_ranking_element($form);
        $this->issue_1_comment_element($form);
        $this->issue_2_ranking_element($form);
        $this->issue_2_comment_element($form);
        $this->issue_3_ranking_element($form);
        $this->issue_3_comment_element($form);
    }
    public function build_home_page_form(&$form){
        $this->state_select_element($form);
    }
    
}