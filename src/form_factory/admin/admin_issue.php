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
        $form["issue_name"] = array(
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
        $form["issue_name"] = array(
            '#type' => 'textfield',
            '#title' => "Issue Name",
            '#required' => true,
            '#size' => "20",
            '#min' => 4,
            '#max' => 80,
        );
        $form_state['input']['edit_issue_name'] = $issue['name'];
    }
    public function create_issue__bill(&$form, $form_State)
    {
        $form['bill_in_congress'] = array(
          '#type' => "select",
          "#title" => "Does this Issue have a corresponding bill in Congress?",
          "#options" => array(
            "yes" => "Yes",
              "no" => "No",
          ),
           '#required' => true,
        );
    }
    public function edit_form_bill_id($issue, &$form, &$form_state)
    {
        $form["bill_id"] = array(
            '#type' => 'textfield',
            '#title' => "Bill Id",
            '#required' => true,
            '#size' => "20",
            '#min' => 4,
            '#max' => 80,
        );
        if($issue['bill_id'] != "n/a")
        {
            $form_state['input']['edit_bill_id'] = $issue['bill_id'];
        }
    }
    public function  create_form_bill_id(&$form, $form_state)
    {
        $form["bill_id"] = array(
            '#type' => 'textfield',
            '#title' => "Bill Id",
            '#size' => "20",
            '#min' => 4,
            '#max' => 80,
            '#states' => array(
                'visible' =>  [':input[name="bill_in_congress"]' => ['value' => "yes"]],
                "and",
                'required' => [':input[name="bill_in_congress"]' => ['value' => "yes"]],
            )
        );
    }
    public function edit_bill_slug($issue, &$form, &$form_state)
    {
        $form["bill_slug"] = array(
            '#type' => 'textfield',
            '#title' => "Bill Slug",
            '#required' => true,
            '#size' => "20",
            '#min' => 4,
            '#max' => 80,
        );
        if($issue['bill_slug'] != "n/a")
        {
            $form_state['input']['edit_bill_slug'] = $issue['bill_id'];
        }
    }
    public function create_bill_slug(&$form, $form_state)
    {
        $form["bill_slug"] = array(
            '#type' => 'textfield',
            '#title' => "Bill Slug",
            '#required' => true,
            '#size' => "20",
            '#min' => 4,
            '#max' => 80,
        );
    }
    public function create_primary(&$form, $form_state)
    {
        $form['primary_issue'] = array(
            '#type' => "select",
            "#title" => "Is this the first time the Issue is being brought up during Washington Seminar?",
            "#options" => array(
                "yes" => "Yes",
                "no" => "No",
            ),
            '#required' => true,
        );
    }
    public function edit_primary($issue, &$form, &$form_state)
    {
        $form['primary_issue'] = array(
            '#type' => "select",
            "#title" => "Is this the first time the Issue is being brought up during Washington Seminar?",
            "#options" => array(
                "yes" => "Yes",
                "no" => "No",
            ),
            '#required' => true,
        );

            $form_state['input']['edit_bill_slug'] = $issue['bill_id'];
    }




}