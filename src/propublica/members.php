<?php
Namespace Drupal\nfb_washington\propublica;
use Drupal\nfb_washington\database\base;

class members extends query_base{
    public $member_first_name;
    public $member_last_name;
    public $member_middle_name;
    public $member_phone_number;
    public $member_district;
    public $member_rank;
    public $member_pp_id;
    public $member_office_address;
    public $member_d_o_b;
    public $member_gender;
    public $member_active;
    public $member_state;
    public $member_party;
    public function get_member_state()
    {return $this->member_state;}
    public function get_member_first_name()
    {return $this->member_first_name;}
    public function get_member_last_name()
    {return $this->member_last_name;}
    public function get_member_middle_name()
    {return $this->member_middle_name;}
    public function get_member_phone_number()
    {return $this->member_phone_number;}
    public function  get_member_district()
    {return $this->member_district;}
    public function get_member_rank()
    {return $this->member_rank;}
    public function get_member_pp_id()
    {return $this->member_pp_id;}
    public function get_office_address()
    {return $this->member_office_address;}
    public function get_member_d_o_b()
    {return $this->member_d_o_b;}
    public function get_member_gender()
    {return $this->member_gender;}
    public function get_member_active()
    {return $this->member_active;}
    public function get_member_party()
    {return $this->member_party;}
    public function set_congress_number()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_config where setting = 'congress_number' and active = '0';";
        $key = 'config_id';
        $this->database = new base();
        $this->database->select_query($query, $key);
        $congress_number = null;
        foreach($this->database->get_result() as $cong_numb)
        {
            if($congress_number == null){
            $cong_numb = get_object_vars($cong_numb);
             $congress_number = $cong_numb['value'];}
        }
        $this->congress_number = $congress_number;
    }
    public function   member_query()
    {
        $api_url = "https://api.propublica.org/congress/v1/".$this->get_congress_number()."/".$this->get_search_criteria_1()."/".$this->get_entity().".json";
        $this->api_url = $api_url;
        $this->set_curl();
        $this->curl_execute_set_propublica_result();
    }
    public function parse_member($member)
    {

        $this->member_first_name = $member['first_name'];
        $this->member_middle_name = $member["middle_name"];
        $this->member_last_name = $member["last_name"];
        $this->member_phone_number = $member["phone"];
        $this->member_office_address = $member['office'];
        if($this->get_search_criteria_1() == "house")
        {$this->member_district = $member["district"];}
        else{$this->member_rank = $member["state_rank"];}
        $this->member_pp_id = $member["id"];
        $this->member_d_o_b = $member["date_of_birth"];
        $this->member_gender = $member["gender"];
        $this->member_party = $member['party'];

        if($member['in_office'] == 1){
            $this->member_active = "true";
        }
        else {$this->member_active = "false";}
        $this->member_active = $member["in_office"];
        $this->member_state = $member['state'];
        \Drupal::logger("washington_sem_debug")->notice("Member In office: ".$this->get_member_first_name(). " ". $this->get_member_last_name().": ".$this->get_member_state()
            ." ".$member['in_office']);
    }
    public function leaving_congress_query()
    {
        $this->api_url = "https://api.propublica.org/congress/v1/".$this->get_congress_number()."/".$this->get_search_criteria_1()."/".$this->get_entity()."/leaving.json";
        $this->set_curl();
        $this->curl_execute_set_propublica_result();
    }
    public function leaving_congress_parse($member)
    {
        $this->member_first_name = $member['first_name'];
        $this->member_middle_name = $member["middle_name"];
        $this->member_last_name = $member["last_name"];
        $this->member_pp_id = $member["id"];
        $this->member_active = "false";
    }







}