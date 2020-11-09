<?php
namespace  Drupal\nfb_washington\form_factory\admin;
class admin_issue
{
    public $database;

    public function issue_switch($issue, &$form, &$form_state)
    {
       if($issue == "create")
       {
          $this->create_new_issue_form($form, $form_state);
       }
       else {
          $this->edit_existing($issue, $form, $form_state);
       }
    }
    public function  create_new_issue_form(&$form, $form_state)
    {
    }
    public function edit_existing($issue, &$form, $form_state)
    {

    }
    public function build_issue_name(&$form, $form_state)
    {
        $form["create_issue_name"] = array(
            '#type' => 'textfield',
            '#title' => "Issue Name",
            '#required' => true,
            '#size' => "20",
            '#min' => 4,
            '#max' => 80,
        );
    }
    public function create_edit_issue_name($issue, &$form, &$form_state)
    {
        $form["edit_issue_name"] = array(
            '#type' => 'textfield',
            '#title' => "Issue Name",
            '#required' => true,
            '#size' => "20",
            '#min' => 4,
            '#max' => 80,
        );
        $form_state['input']['edit_edit_issue_name'] = $issue['name'];
    }
    public function create_issue__bill(&$form, $form_State)
    {

    }


}