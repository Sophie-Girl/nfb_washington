<?php
Namespace Drupal\nfb_washington\propublica;

class committee extends members
{
    public $committee_id;
    public function get_committee_id()
    {return $this->committee_id;}
    public $is_chair;
    public function get_is_chair()
    {return $this->is_chair;}
    public $ranking_member;
    public function get_ranking_member()
    {return $this->ranking_member;}
    public function general_committee_search()
    {
        $this->api_url = "https://api.propublica.org/congress/v1/".$this->get_congress_number()."/".$this->get_search_criteria_1()."/".$this->get_entity().".json";
        $this->set_curl();
        $this->curl_execute_set_propublica_result();
    }
    public function specific_committee_search()
    {
        $this->api_url = "https://api.propublica.org/congress/v1/".$this->get_congress_number()."/".$this->get_search_criteria_1()."/".$this->get_entity()."/".$this->get_committee_id().".json";
        $this->set_curl();
        $this->curl_execute_set_propublica_result();
    }
    public function parse_general_query($committee, $committee_name)
    {
        if($this->get_committee_id() == null) {
            if ($committee['name'] == $committee_name) {
                $this->committee_id = $committee['id'];
            }
        }
    }
    public function specific_committee_parse($com_member)
    {
        $this->member_pp_id = $com_member['id'];
        $this->member_active = '0';
        if($com_member['id'] == $this->get_is_chair())
        {$chair = 1;}  else {$chair = 0;}
        $this->is_chair = $chair;
        if($com_member['id'] == $this->get_ranking_member())
        {$rm = 1;} else {$rm = 0;}
        $this->ranking_member = $rm;
    }
    public function member_maintenance($con_member)
    {
        if($con_member['id'] == $this->get_member_pp_id())
        {
            $this->specific_committee_parse($con_member);

        }
    }
    public function set_chair_and_ranking_member()
    {
       $result =  $this->get_propublica_result();
       $this->ranking_member = $result['results']['0']['ranking_member_id'];
       $this->is_chair = $result['result']['0']['chair_id'];
    }




}