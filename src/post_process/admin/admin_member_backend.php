<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\nfb_washington\civicrm_drupal_link\drupal_member_civi_contact_link;

class admin_member_backend
{
    public $link;
    public function __construct(drupal_member_civi_contact_link $drupal_member_civi_contact_link)
    {
        ini_set('max_execution_time', 500);
        $this->link = $drupal_member_civi_contact_link;
    }
    public function backend($mode)
    {
        if($mode == "new_congress")
        $this->link->new_congress_run_through();
        else
        {$this->link->mid_congress_maint();}
    }

}