<?php
Namespace Drupal\nfb_washington\propublica;
use Drupal\nfb_washington\database\base;

class query_base
{
    public $api_key;
    public function get_api_key()
    {return $this->api_key;}
    public $entity; // Connell, Sophie: What are you querying (members, committe)
    public function  get_entity()
    {return $this->entity;}
    public $congress_number;
    public function get_congress_number()
    {return $this->congress_number;}
    public $search_criteria_1;
    public function get_search_criteria_1()
    {return $this->search_criteria_1;}
    public $search_criteria_2;
    public function get_search_criteria_2()
    {return $this->search_criteria_2;}
    public $database;
    public function set_api_key()
    {
        $this->database = new base(); $api_key = "";
        $query = "select * from nfb_washington_config where setting = 'pp_id' and active = '0';";
        $key= "config_id"; $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $api_keys)
        {
            if($api_key == "")
            {$api_key = $api_keys['value'];}
        }
        $this->api_key = $api_key;
    }
}