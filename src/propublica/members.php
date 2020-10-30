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
    public function get_member_first_name()
    {return $this->member_first_name;}
    public function get_member_last_name()
    {return $this->member_last_name;}
    public function get_member_middle_name()
    {return $this->member_middle_name;}
    public function member_phone_number()
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
    public function set_congress_number()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_config where setting = 'congress_number' and active = '0';";
        $key = 'config_id';
        $this->database = new base();
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $cong_numb)
        {
            $cong_numb = get_object_vars($cong_numb);
            $this->congress_number = $cong_numb['value'];
        }
    }
    public function   member_query()
    {
        $api_url = "https://api.propublica.org/congress/v1/".$this->get_congress_number()."/".$this->get_search_criteria_1()."/".$this->get_entity().".json";
        $this->api_url = $api_url;
    }






}