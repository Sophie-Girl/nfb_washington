<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;

class admin_issue_backend
{
    public $database;
    public $primary_issue;
    public $created_user;
    public $modified_user;
    public $bill_id;
    public $bill_slug;
    public $primary_issue_id;
    public function get_primary_issue()
    {return $this->primary_issue;}
    public function get_created_user()
    {return $this->created_user;}
    public function get_modified_user()
    {return $this->modified_user;}
    public function get_bill_id()
    {return $this->bill_id;}
    public function  get_bill_slug()
    {return $this->bill_slug;}
    public function get_primary_issue_id()
    {return $this->primary_issue_id;}
    public function issue_backend($issue, FormStateInterface $form_state)
    {

    }
    public function set_up_form_values($issue, FormStateInterface $form_state)
    {
        $this->convert_true_false($form_state);
        if($issue == "create")
        {$this->created_user_set();}
        $this->modified_user_set();

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
    public function issue_switch($issue, FormStateInterface $form_state)
    {
        if($issue = "create")
        {
            $this->create_backend($form_state);
        }
        else
        {

        }
    }
    public function create_backend(FormStateInterface $form_state)
    {
        $fields = array(
            'issue_name'  => $form_state->getValue("issue_name"),
            "bill_id" => $this->get_bill_id(),
            "bill_slug" => $this->get_bill_slug(),
            "civicrm_id" => "0",
            "primary_issue" => $this->get_primary_issue(),
            "primary_issue_id" => $this->get_primary_issue_id(),
            "created_user" => $this->get_created_user(),
            "modified_user" => $this->get_modified_user(),
        );
        $table = "nfb_washington_issues";
        $this->database = new base();
        $this->database->insert_query($table, $fields);
    }


}