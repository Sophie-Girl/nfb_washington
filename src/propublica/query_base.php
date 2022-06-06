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
    public $curl;
    public $propublica_result;
    public $api_url;
    public function get_propublica_result(){
        return $this->propublica_result;
    }
    public function get_api_url()
    {
        return $this->api_url;
    }
    public function get_curl()
    {return $this->curl;}
    public $database;
    public function set_api_key()
    {
        $this->database = new base(); $api_key = "";
        $query = "select * from nfb_washington_config where setting = 'pp_id' and active = '0';";
        $key= "config_id"; $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $api_keys)
        {
            $api_keys = get_object_vars($api_keys);
            if($api_key == "")
            {$api_key = $api_keys['value'];}
        }
        $this->api_key = $api_key;
    }
    public function  set_curl()
    {
        $propublica_curl = curl_init();
        // set up curl
        if ($this->get_api_url() != false) {
            \drupal::logger("steal_this_url")->notice("curl url: ".$this->get_api_url());
            // if a valid url has been set execute this
            curl_setopt($propublica_curl, CURLOPT_HTTPHEADER, array(
                'X-API-Key:' . $this->get_api_key()));
            // set the api key
            curl_setopt($propublica_curl, CURLOPT_HTTPGET, 1);
            /* Ensure it is a GET request */
            curl_setopt($propublica_curl, CURLOPT_SSL_VERIFYPEER, false);
            /* Turn off SSL */
            curl_setopt($propublica_curl, CURLOPT_RETURNTRANSFER, true);
            /* Make sure a value is always returned*/
            curl_setopt($propublica_curl, CURLOPT_URL, $this->get_api_url());
            /* Set URL */
            $this->curl = $propublica_curl;
        }
    }
    public function curl_execute_set_propublica_result()
    {
        $Curl_result = curl_exec($this->get_curl());
        // execute the curl
        if ($Curl_result === false) {

            $Curl_info = curl_getinfo($this->get_curl());
            echo PHP_EOL.$Curl_info.PHP_EOL;
            curl_close($this->get_curl());
            exit;
        }
        else
        {
            $Propublica_results = json_decode($Curl_result, true);
            curl_close($this->get_curl());
            $this->propublica_result = $Propublica_results;
        }

    }



}