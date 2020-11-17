<?php
Namespace Drupal\nfb_washington\propublica;

class committee extends members
{
    public $committee_id;
    public function get_committee_id()
    {return $this->committee_id;}
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
        $this->member_active = $com_member['0'];
    }
    public function member_maintenance($con_member)
    {
        if($con_member['id'] == $this->get_member_pp_id())
        {
            $this->specific_committee_parse($con_member);

        }
    }




}