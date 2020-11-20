<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\nfb_washington\database\base;

class admin_com_iss_link
{
    public $database;

    public function build_form_array($form, $form_state)
    {
        $this->initial_markup($form, $form_state);
        $this->issue_select($form, $form_state);
        $this->committee_select($form, $form_state);
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",
        );
    }
    public function initial_markup(&$form, $form_state){
        $form['intro'] = array(
          '#type' => "item",
          "#markup"=> "<p>You cna Link and issue to a committee in congress, so that was if a member 
           serves on that committee and it is relevant to an issue. It will show up on 
            their member page in hte washington seminar report</p>"
        );
    }
    public function issue_select(&$form, $form_state)
    {
        $form['issue_value'] = array(
            '#type' => "select",
            '#title' => "Issue",
            "#required" => true,
            "#options" => $this->issue_options(),
        );
    }
    public function committee_select(&$form, $form_state)
    {
        $form['committee_value'] = array(
            '#type' => "select",
            '#title' => "Committee",
            "#required" => true,
            "#options" => $this->commitee_options(),
        );
    }
    public function issue_options()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues;";
        $key = "issue_id"; $options = [];
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            $options[$issue['issue_id']] = $issue['issue_name'];
        }
        $this->database = null;
        return $options;
    }
    public function committee_options()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee;";
        $key = "committee_id"; $options = [];
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $committee)
        {
            $committee = get_object_vars($committee);
            $options[$committee['committee_id']] = $committee['propublica_id'].": ".$committee['committee_name'];
        }
        $this->database = null;
        return $options;
    }
}