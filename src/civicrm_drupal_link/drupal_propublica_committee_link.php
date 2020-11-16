<?php
Namespace Drupal\nfb_washington\civicrm_drupal_link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\propublica\committee;

class drupal_propublica_committee_link
{
    public $database;
    public $propublica;
    public  $committee_name;
    public $drupal_committee_id;
    public $drupal_member_id;
    public function get_committee_name()
    {return $this->committee_name;}
    public function get_drupal_committee_id()
    {return $this->drupal_committee_id;}
    public function get_drupal_member_id()
    {return $this->drupal_member_id;}
    public function __construct(committee $committee)
    {
        $this->propublica = $committee;
    }
    public function committee_add_edit_backend(FormStateInterface $form_state)
    {
        if($form_state->getValue("committee_value") == "add")
        {
            $this->set_up_initial_query_add_committee($form_state);
            $this->deduplication_check();
            if(!$this->get_drupal_committee_id())
            {$this->create_committee_drupal_record();}
        }
        else {

        }
    }
    public function set_up_initial_query_add_committee(FormStateInterface $form_state)
    {
        $this->committee_name = $form_state->getValue("committee_name");
        $this->propublica->entity = "committees";
        $this->establish_propublica_dependencies();
        $this->propublica->search_criteria_1 = $form_state->getValue("committee_chamber");
        $this->propublica->general_committee_search();
        $this->initial_addition_run_through();
    }
    public function initial_addition_run_through()
    {   $this->propublica->committee_id = "null";
        foreach($this->propublica->get_propublica_result()['results']['0']['committees'] as $committee)
        {
            $committee_name = $this->get_committee_name();
            $this->propublica->parse_general_query($committee, $committee_name);
        }
    }
    public function deduplication_check()
    {
        $this->drupal_committee_id = null;
        $this->database = new base();
        $query = " select committee_id from nfb_washington_committee where propublica_id = '".$this->propublica->committee_id."';";
        $key = "propublica_id";
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->duplicate_check_step_2($result);
        if($this->get_drupal_committee_id() != null)
        {

        }

    }
    public function duplicate_check_step_2($result)
    {
      $result = get_object_vars($result[$this->propublica->get_committee_id()]);
      if($result['committee_id'])
      {$this->drupal_committee_id = $result['committee_id'];}
    }
    public function establish_propublica_dependencies()
    {
        $this->propublica->set_api_key(); $this->propublica->set_congress_number();
    }
    public function set_up_specific_query_for_edits_and_members(FormStateInterface $form_state)
    {
        if($form_state->getValue("committee_value") != "add")
        {$this->drupal_committee_id = $form_state->getValue("committee_value");}
        $this->propublica->entity = "committees";
        if(!$this->get_drupal_committee_id())
        {$this->database_value_lookup();}
        $this->establish_propublica_dependencies();
    }
    public function database_value_lookup()
    {
        $this->database = new base();
        $query = "select committee_id, propublica_id from nfb_washington_committee where committee_id == '".$this->get_drupal_committee_id()."';";
        $key = 'committee_id';
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->loop_through_committee_value($result);
        $this->database = null;
    }
    public function loop_through_committee_value($result)
    {
        $result = get_object_vars($result[$this->get_drupal_committee_id()]);
        $this->propublica->committee_id = $result['propublica_id'];
    }
    public function find_drupal_comittee_id()
    {
        $this->database = new base();
        $query = "select committee_id, propublica_id from nfb_washington_committee where propublica_id == '".$this->propublica->get_committee_id()."';";
        $key = 'propublica_id';
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->find_committee_id($result);
        $this->database = null;
    }
    public function find_committee_id($result)
    {
        $result = get_object_vars($result[$this->propublica->get_committee_id()]);
        $this->drupal_committee_id = $result['committee_id'];
    }
    public function create_committee_drupal_record()
    {
        $this->database = new base();
        $fields = array(
            'propublica_id' => $this->propublica->get_committee_id(),
            'committee_name' => $this->get_committee_name(),
            'chamber' => $this->propublica->get_search_criteria_1(),
            'active' => '0',
        );
        $table = "nfb_washington_committee";
        $this->database->insert_query($table, $fields);
        $this->database = null;
        $this->find_drupal_comittee_id();
    }
    public function member_committee_run_through()
    {
        foreach($this->propublica->get_propublica_result()['result']['0']['current_members'] as $com_mem)
        {
            $this->drupal_member_id = null;
            $this->propublica->specific_committee_parse($com_mem);
            $this->find_member_id();


        }
    }
    public function find_member_id()
    {
        $this->database = new base;
        $query = "select member_id, propublica_id from nfb_wasington_member where propublica_id = '".$this->propublica->get_member_pp_id()."';";
        $key = "propublica_id";
        $this->database->select_query($query, $key);
        $result = $this->propublica->get_propublica_result();
        $this->set_drupal_member_id($result);
        $this->database = null;
    }
    public function set_drupal_member_id($result)
    {
        $result = get_object_vars($result[$this->propublica->get_member_pp_id()]);
        if($result['member_id']){
        $this->drupal_member_id = $result['member_id'];}
    }
    public function duplicate_committee_member_check()
    {
        $com_member_id = null;
        $this->database = new base();
        $query = "select * from nfb_washington_committee_mem where committee_id = '".$this->get_drupal_committee_id()."' and member_id = '".$this->get_drupal_member_id()."';";
        $key = "member_id";
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->duplicate_com_mem_2($result, $com_member_id);
        $this->database = null;
    }
    public function duplicate_com_mem_2($result, &$com_member_id)
    {
        $result = get_object_vars($result[$this->get_drupal_member_id()]);
        if($result['com_mem_id']){
        $com_member_id = $result['com_mem_id'];}
    }



}