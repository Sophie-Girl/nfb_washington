<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;

class admin_issue_backend
{
    public $database;
    public $primary_issue;
    public $created_user;
    public $modified_user;
    public function get_primary_issue()
    {return $this->primary_issue;}
    public function get_created_user()
    {return $this->created_user;}
    public function get_modified_user()
    {return $this->modified_user;}
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
    public function created_user_set()
    {
        $user = \Drupal::currentUser()->getUsername();
        $this->created_user = $user;
    }
    public function modified_user_set()
    {
        $user = \drupal::currentUser()->getUsername();
        $this->modified_user = $user;
    }

}