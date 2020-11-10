<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;

class admin_issue_backend
{
    public $database;
    public $primary_issue;
    public function get_primary_issue()
    {

    }
    public function issue_backend($issue, FormStateInterface $form_state)
    {

    }
    public function  convert_true_false(FormStateInterface $form_state)
    {
        if($form_state->getValue("primary_issue") == "yes")
        {
            $this->primary_issue = "0";
        }
        else {$this->primary_issue = "1";}
    }
}