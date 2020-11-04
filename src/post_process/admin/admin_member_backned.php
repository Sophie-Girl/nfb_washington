<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\nfb_washington\civicrm_drupal_link\drupal_member_civi_contact_link;

class admin_member_backend
{
    public $link;
    public function __construct(drupal_member_civi_contact_link $drupal_member_civi_contact_link)
    {
        $this->link = $drupal_member_civi_contact_link;
    }
    public function backend()
    {
        $this->link->set_up_general_member_process();
    }

}