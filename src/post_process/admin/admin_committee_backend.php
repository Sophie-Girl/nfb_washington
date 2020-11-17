<?php
Namespace Drupal\nfb_washington\post_process\admin;
use drupal\core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm_drupal_link\drupal_propublica_committee_link;
use Drupal\nfb_washington\propublica\committee;

class  admin_committee_backend
{
    public $link;
    public function backend(FormStateInterface $form_state)
    {
        $this->link = $this->dependency_injection();
        $this->link->committee_add_edit_backend($form_state);
    }
    public function dependency_injection()
    {
        $propublica  =  new committee();
        $link = new drupal_propublica_committee_link($propublica);
        return $link;
    }
}