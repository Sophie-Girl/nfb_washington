<?php
namespace  Drupal\nfb_washington\form_factory\admin;
class admin_issue
{
    public $database;

    public function issue_switch($issue, &$form, $form_state)
    {
       if($issue == "create")
       {
          $this->create_new_issue_form($form, $form_state);
       }
       else {
          $this->edit_existing($issue, &$form, $form_state);
       }
    }
    public function  create_new_issue_form(&$form, $form_state)
    {
    }
    public function edit_existing($issue, &$form, $form_state)
    {

    }

}