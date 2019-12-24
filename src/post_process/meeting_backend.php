<?php
Namespace Drupal\nfb_washington\post_process;

class  meeting_backend extends base
{
    public function meeting_person($form_State)
    {
        $this->dependency_injection($form_State);
    }
}