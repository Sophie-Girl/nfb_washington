<?php
Namespace Drupal\nfb_washington\form_factory;
class form_factory extends markup_elements
{
    public function build_new_meeting_time(&$form)
    {
        $this->contact_first_name_element($form);
        $this->contact_last_name_element($form);
        $this->contact_email_element($form);
        $this->meeting_day_element($form);
        $this->meeting_time_element($form);
        $this->meeting_comments_element($form);
    }
    public function build_update_mmeting_form(&$form)
    {
        $this->contact_first_name_element($form);
        $this->contact_last_name_element($form);
        $this->contact_email_element($form);
        $this->meeting_day_element($form);
        $this->meeting_time_element($form);
        $this->meeting_comments_element($form);
    }
    
}