<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        $this->set_up_form_values($issue, $form_state);
        $this->issue_switch($issue, $form_state);
        $this->finish_redirect($issue, $form_state);
    }
    public function set_up_form_values($issue, FormStateInterface $form_state)
    {
        $this->convert_true_false($form_state);
        if($issue == "create")
        {$this->created_user_set();}
        $this->modified_user_set();
        $this->set_up_primary_issue_id($form_state);
        $this->set_bill_slugs_and_id($form_state);
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
    public function  set_up_primary_issue_id(FormStateInterface $form_state)
    {
        if($form_state->getValue("derivative_issue") == "")
        {$this->primary_issue_id = "null";}
        else {$this->primary_issue_id = $form_state->getValue("derivative_issue");}
    }
    public function set_bill_slugs_and_id(FormStateInterface $form_state)
    {
        if($form_state->getValue("bill_id") == "")
        {$this->bill_id = "n/a";} else {$this->bill_id = $form_state->getValue("bill_id");}
        if($form_state->getValue("bill_slug") == "")
        {$this->bill_slug = "n/a";} else {$this->bill_slug = $form_state->getValue("bill_slug");}
    }
    public function issue_switch($issue, FormStateInterface $form_state)
    {
        if($issue == "create")
        {
            $this->create_backend($form_state);
        }
        else
        {
            $this->edit_backend($issue, $form_state);
        }
    }
    public function create_backend(FormStateInterface $form_state)
    {
        $fields = array(
            'issue_name'  => $form_state->getValue("issue_name"),
            "bill_id" => $this->get_bill_id(),
            "bill_slug" => $this->get_bill_slug(),
            "civicrm_id" => "0",
            "primary_status" => $this->get_primary_issue(),
            "primary_issue_id" => $this->get_primary_issue_id(),
            "created_user" => $this->get_created_user(),
            "last_modified_user" => $this->get_modified_user(),
        );
        $table = "nfb_washington_issues";
        $this->database = new base();
        $this->database->insert_query($table, $fields);
        $this->database = null;
    }
    public function edit_backend($issue, FormStateInterface $form_state)
    {
        $this->database = new base();
        $this->issue_name_update($query, $issue, $form_state);
        $this->database->update_query($query);
        $this->bill_id_update($query, $issue);
        $this->database->update_query($query);
        $this->bill_slug_update($query, $issue);
        $this->database->update_query($query);
        $this->primary_issue_update($query, $issue);
        $this->database->update_query($query);
        $this->primary_issue_id_update($query, $issue);
        $this->database->update_query($query);
        $this->modified_user_update($query, $issue);
        $this->database->update_query($query);
        $this->database = null;

    }
    public function issue_name_update(&$query, $issue, FormStateInterface $form_state)
    {
        $query = "update nfb_washington_issues
        set issue_name = '".$form_state->getValue("issue_name")."'
        where issue_id = '".$issue."';";
    }
    public function bill_id_update(&$query, $issue)
    {
        $query = "update nfb_washington_issues
        set bill_id = '".$this->get_bill_id()."'
        where issue_id = '".$issue."';";
    }
    public function bill_slug_update(&$query, $issue)
    {
        $query = "update nfb_washington_issues
        set bill_slug = '".$this->get_bill_slug()."'
        where issue_id = '".$issue."';";
    }
    public function primary_issue_update(&$query, $issue)
    {
        $query = "update nfb_washington_issues
        set primary_status = '".$this->get_primary_issue()."'
        where issue_id = '".$issue."';";
    }
    public function primary_issue_id_update(&$query, $issue)
    {
        $query = "update nfb_washington_issues
        set primary_issue_id = '".$this->get_primary_issue_id()."'
        where issue_id = '".$issue."';";
    }
    public function modified_user_update(&$query, $issue)
    {
        $query = "update nfb_washington_issues
        set last_modified_user = '".$this->get_modified_user()."'
        where issue_id = '".$issue."';";
    }
    public function finish_redirect($issue, FormStateInterface $form_state)
    {
        if($issue == "create")
        {$message = "Issue created for ".$form_state->getValue("issue_name");}
        else {$message = "Issue Updated";}
        $form_state = null;
        drupal_set_message($message);
        $ender = new RedirectResponse('/nfb_washington/admin/issues');
        $ender->send(); $ender = null;
        return;

    }



}