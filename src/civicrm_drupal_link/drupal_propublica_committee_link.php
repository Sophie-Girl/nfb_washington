<?php
Namespace Drupal\nfb_washington\civicrm_drupal_link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\propublica\committee;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
            $this->set_up_specific_query_for_edits_and_members($form_state);
            $this->member_committee_run_through();
            $error_status = "none";
            $this->finish_redirect($error_status);
        }
        else {
            $this->update_committee($form_state);
            $error_status = "none";
            $this->finish_redirect($error_status);
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
    {   $this->propublica->committee_id = null;
        $committee_name = $this->get_committee_name();

        foreach($this->propublica->get_propublica_result()['results']['0']['committees']as $committee)
        {
            $this->propublica->parse_general_query($committee, $committee_name);
        }
    }
    public function deduplication_check()
    {
        $this->drupal_committee_id = null;
        $this->database = new base();
        $query = " select committee_id from nfb_washington_committee where propublica_id = '".$this->propublica->get_committee_id()."';";
        $key = "propublica_id";
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->duplicate_check_step_2($result);
        if($this->get_drupal_committee_id() != null)
        {
            $error_Status = "duplicate";
            $this->finish_redirect($error_Status);
        }
        else {
            if($this->propublica->get_committee_id() != null)
            {
            $this->create_committee_drupal_record(); }
            else {
                $error_Status = "notfound";
                $this->finish_redirect($error_Status);
            }
        }

    }
    public function duplicate_check_step_2($result)
    {
        $committee_id = null;
        foreach ($result as $committee)
        { $committee = get_object_vars($committee);
        if($committee_id == null)
        {$committee_id = $committee['committee_id'];}
        }
        $this->drupal_committee_id = $committee_id;
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
        if(!$this->propublica->get_committee_id())
        {$this->database_value_lookup();}
        $this->establish_propublica_dependencies();
        if(!$this->propublica->get_search_criteria_1())
        {$this->propublica->search_criteria_1 = $form_state->getValue("committee_chamber");}
        $this->propublica->specific_committee_search();
    }
    public function database_value_lookup()
    {
        $this->database = new base();
        $query = "select committee_id, propublica_id from nfb_washington_committee where committee_id = '".$this->get_drupal_committee_id()."';";
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
        $query = "select committee_id, propublica_id from nfb_washington_committee where propublica_id = '".$this->propublica->get_committee_id()."';";
        $key = 'propublica_id';
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->find_committee_id($result);
        $this->database = null;
    }
    public function find_committee_id($result)
    {
        $committee_id = null;
        foreach ($result as $committee)
        {
            $committee = get_object_vars($committee);
            if($committee_id == null)
            {
                $committee_id = $committee['committee_id'];
            }
        }
        $this->drupal_committee_id = $committee_id;
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
        $this->propublica->set_chair_and_ranking_member();
        foreach($this->propublica->get_propublica_result()['results']['0']['current_members'] as $com_mem)
        {
            $this->drupal_member_id = null;
            $this->propublica->specific_committee_parse($com_mem);
            $this->find_member_id();
        }
    }
    public function find_member_id()
    {
        $this->database = new base;
        $query = "select member_id, propublica_id from nfb_washington_members where propublica_id = '".$this->propublica->get_member_pp_id()."';";
        $key = "propublica_id";
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->set_drupal_member_id($result);
        $this->database = null;
        $this->duplicate_committee_member_check();
    }
    public function set_drupal_member_id($result)
    {
        $member_id = null;
        foreach ($result as $member)
        {
            $member = get_object_vars($member);
            if($member_id == null)
            {
                $member_id = $member['member_id'];
            }
        }
        $this->drupal_member_id = $member_id;
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
        $com_member_id = null;
        foreach ($result as $con_mem) {
            if ($com_member_id == null) {
                $com_member_id = $con_mem['com_mem_id'];
            }}
        if($com_member_id == null)
        {  $this->create_con_mem_record();}
    }
    public function create_con_mem_record()
    {
        $this->database = new base();
        $fields = array(
          'committee_id' => $this->get_drupal_committee_id(),
          'member_id' => $this->get_drupal_member_id(),
          "active" => '0',
        );
        $table = "nfb_washington_committee_mem";
        $this->database->insert_query($table, $fields);
        $this->database = null;
    }
    public function finish_redirect($error_status)
    {
        if($error_status == "duplicate")
        {$message = "Error: The Committee already exists. Please enter a new committee";
            \Drupal::messenger()->addError($message);
            $ender = new RedirectResponse('/nfb_washington/admin/committee/add');
            $ender->send(); $ender = null;
            return;
        }
        elseif( $error_status == "nofound")
        {
            $message = "Error: The Committee Name provided, ".$this->get_committee_name().", is not matching anything in Propublica. Please try again";
            \Drupal::messenger()->addError($message);
            $ender = new RedirectResponse('/nfb_washington/admin/committee/add');
            $ender->send(); $ender = null;
            return;
        }
        elseif($error_status == "maint")
        {
            $message = "Committee Members Updated";
            \Drupal::messenger()->addMessage($message);
            $ender = new RedirectResponse('/nfb_washington/admin/committees');
            $ender->send(); $ender = null;
        return;
        }
        else {
        $message = "Committee Record Created/Updated";
            \Drupal::messenger()->addMessage($message);
        $ender = new RedirectResponse('/nfb_washington/admin/committees');
        $ender->send(); $ender = null;}
        return;
    }
    public function update_committee(FormStateInterface $form_state)
    {
        $this->database = new base();
        $query = "update nfb_washington_committee
        set committee_name = '".$form_state->getValue("committee_name")."'
        where committee_id = '".$form_state->getValue("committee_value")."';";
        $this->database->update_query($query);
        $this->database = null;

    }
    public function maint_backend(FormStateInterface $form_state)
    {
        $this->drupal_committee_id = $form_state->getValue("committee_value");
        $this->find_needed_values();
        $this->establish_propublica_dependencies();
        $this->specific_committee_query();
        $this->remove_old_member();
        $error_status = "maint";
        $this->finish_redirect($error_status);


    }
    public function find_needed_values()
    {   $committee_pp_id = null; $committee_chamber = null;
        $this->database = new base();
        $query = "select * from nfb_washington_committee;";
        $key = "committee_id";
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $committee)
        {
            $committee = get_object_vars($committee);
            if($committee_chamber == null)
            {$committee_chamber = $committee['committee_chamber'];}
            if($committee_pp_id == null)
            {$committee_pp_id = $committee['propublica_id'];}
        }
        $this->propublica->search_criteria_1 = $committee_chamber;
        $this->propublica->committee_id = $committee_pp_id;
        $this->propublica->entity = "committees";
        $this->database = null;
    }
    public function specific_committee_query()
    {
        $this->propublica->specific_committee_search();
        $this->member_committee_run_through();
    }
    public function remove_old_member()
    {
        foreach($this->propublica->get_propublica_result()['results']['0']['former_members'] as $com_mem)
        {
            $this->drupal_member_id = null;
            $this->propublica->specific_committee_parse($com_mem);
            $this->find_old_member();
        }
    }
    public function find_old_member()
    {
        $this->database = new base;
        $query = "select member_id, propublica_id from nfb_washington_members where propublica_id = '".$this->propublica->get_member_pp_id()."';";
        $key = "propublica_id";
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->set_drupal_member_id($result);
        $this->database = null;
        $this->old_committee_member_check();
    }
    public function old_committee_member_check()
    {
        $com_member_id = null;
        $this->database = new base();
        $query = "select * from nfb_washington_committee_mem where committee_id = '".$this->get_drupal_committee_id()."' and member_id = '".$this->get_drupal_member_id()."';";
        $key = "member_id";
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->old_com_mem_2($result, $com_member_id);
        $this->database = null;
    }
    public function old_com_mem_2($result)
    {
        $com_member_id  = null;
        foreach ($result as $con_mem) {
            $con_mem = get_object_vars($con_mem);
            if ($com_member_id == null) {
                $com_member_id = $con_mem['com_mem_id'];
            }}
        if($com_member_id != null)
        { $this->update__member_commitee($com_member_id);}
    }
    public function update__member_commitee($com_member_id)
    {
        $this->database = new base();
        $query  = "update nfb_washington_committee_mem
        set active = 1
        where com_mem_id '".$com_member_id."';";
        $this->database->update_query($query);
        $this->database = null;
    }







}