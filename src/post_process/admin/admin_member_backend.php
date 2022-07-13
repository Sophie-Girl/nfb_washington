<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\nfb_washington\civicrm_drupal_link\drupal_member_civi_contact_link;
use Drupal\nfb_washington\post_process\admin\admin_set_meetings;
class admin_member_backend
{
    public $link;
    public $meeting;
    public function __construct(drupal_member_civi_contact_link $drupal_member_civi_contact_link)
    {
        $this->link = $drupal_member_civi_contact_link;
    }
    public function backend($mode)
    {
        ini_set('max_execution_time', 500);
        if($mode == "new_congress"){
        $this->link->new_congress_run_through();}
        else
        {$this->link->mid_congress_maint();
            $this->link->new_congress_run_through();}
        $this->dummy_meetings();
    }
    public function dummy_meetings()
    {
        $script = new admin_set_meetings();
        $script->create_dummy_meetings();
        $script = null;
    }

}