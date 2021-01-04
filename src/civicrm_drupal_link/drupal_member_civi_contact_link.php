<?php
Namespace Drupal\nfb_washington\civicrm_drupal_link;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\propublica\members;

class drupal_member_civi_contact_link
{
    public $civi_query;
    public $propublica_query;
    public $drupal_civicrm_id;
    public $database;
    public function  get_drupal_civicrm_id()
    {return $this->drupal_civicrm_id;}
    public function  __construct(civi_query $civi_query, members $members)
    {
        $this->civi_query = $civi_query;
        $this->propublica_query = $members;
    }
    public function new_congress_run_through()
    {

        \Drupal::logger("nfb_Washington_debug")->notice("I am going to get to the import");
     //   $this->set_up_general_member_process();
        $this->set_up_old_congress_maintenance_query();
    }
    public function set_up_general_member_process()
    {
        $this->propublica_query->set_api_key();
        $this->propublica_query->set_congress_number();
        $this->propublica_query->entity = "members";
        $this->propublica_query->search_criteria_1 = "house";
        $this->propublica_query->member_query();
        \DRupal::logger('okay_checking_a_thing')->notice(print_r($this->propublica_query->get_propublica_result(), true));
        $this->general_run_through();
        $this->propublica_query->search_criteria_1 = "senate";
        $this->propublica_query->member_query();
        $this->general_run_through();
    }
    public function set_up_old_congress_maintenance_query()
    {
        $this->propublica_query->set_api_key();
        $this->propublica_query->set_congress_number();
        $this->propublica_query->entity = "members";
        $this->propublica_query->search_criteria_1 = "house";
        $this->propublica_query->congress_number = (int)$this->propublica_query->get_congress_number() - 1;

        $this->propublica_query->leaving_congress_query();
        \DRupal::logger('okay_checking_another_thing')->notice(print_r($this->propublica_query->get_propublica_result(), true));
        $this->removal_run_through();
        $this->propublica_query->search_criteria_1 = "senate";
        $this->propublica_query->leaving_congress_query();
        $this->removal_run_through();

    }
    public function mid_congress_maint()
    {
        $this->propublica_query->set_api_key();
        $this->propublica_query->set_congress_number();
        $this->propublica_query->entity = "members";
        $this->propublica_query->search_criteria_1 = "house";
        $this->propublica_query->leaving_congress_query();
        $this->removal_run_through();
        $this->propublica_query->search_criteria_1 = "senate";
        $this->propublica_query->leaving_congress_query();
        $this->removal_run_through();
    }
    public function general_run_through()
    {
        foreach($this->propublica_query->get_propublica_result()['results']['0']['members'] as $member)
        {$this->propublica_query->parse_member($member);
        if($this->propublica_query->get_member_state()  != "GU" &&
            $this->propublica_query->get_member_state()  != "AS"  &&
        $this->propublica_query->get_member_state()  != "VI"  &&
            $this->propublica_query->get_member_state()  != "MP" &&
            $this->propublica_query->get_member_state()  != "UM" &&
        $this->propublica_query->get_member_state() != "FM"&&
            $this->propublica_query->get_member_state() != "MH" &&
            $this->propublica_query->get_member_state() != "PW"
        )
        {
            $this->civi_processes();
            $this->database_process();
        }

        }
    }
    public function civi_processes(){
        $this->deduplication();
        $this->address_and_phone();
        $this->active_inactive();
    }
    public function deduplication()
    {
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_params = array(
            'sequential' => 1,
            'contact_sub_type' => "Congressional_Representative",
            'first_name' => $this->propublica_query->get_member_first_name(),
            'last_name' => $this->propublica_query->get_member_last_name(),
        );
        $this->civi_query->civi_query();
        if($this->civi_query->get_civicrm_result()['count'] > 0)
        {

            $this->drupal_civicrm_id =  $this->civi_query->get_civicrm_result()['values']['0']['contact_id'];
            $this->convert_gender();
            $this->update_congressional_details();

        }
        else {
            $this->convert_gender();
            $this->create_congressional_civi_record();
        }
    }
    public function address_and_phone()
    {
        $this->civi_query->civi_entity = "Address";
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_params = array(
            'contact_id' => $this->get_drupal_civicrm_id(),
            'sequential' => 1,
            'state_province_id' => 1050,
            'city' => "Washington",
        );
        $this->civi_query->civi_query();
        if($this->civi_query->get_civicrm_result()['count'] > 0)
        {

            $address_id = $this->civi_query->get_civicrm_result()['values']['0']['id'];
            $this->update_address($address_id);
        }
        else{
            $this->create_address();
        }
        $this->Phone_number();
    }
    public function update_address($address_id)
    {
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_entity = "Address";
        $this->civi_query->civi_params = array(
            'id' => $address_id,
            'street_address' => $this->propublica_query->get_office_address(),
        );
        $this->civi_query->civi_query();
    }
    public function create_address()
    {
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_entity = "Address";
        $this->civi_query->civi_params = array(
            'contact_id' => $this->get_drupal_civicrm_id(),
            'street_address' => $this->propublica_query->get_office_address(),
            "location_type_id" => "Work",
            'state_province_id' => 1050,
            'city' => "Washington",
        );
        $this->civi_query->civi_query();

    }
    public function  update_congressional_details()
    {
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_params = array(
            'id' => $this->get_drupal_civicrm_id(),
            'first_name' => $this->propublica_query->get_member_first_name(),
            'middle_name' => $this->propublica_query->get_member_middle_name(),
            'last_name' => $this->propublica_query->get_member_last_name(),
            'birth_date' => $this->propublica_query->get_member_d_o_b(),
            'gender_id' => $this->propublica_query->get_member_gender(),
        );
        $this->civi_query->civi_query();
    }
    public function convert_gender()
    {
        switch ($this->propublica_query->get_member_gender())
        {
            case "M":
                $this->propublica_query->member_gender = "male"; break;
            case "F":
                $this->propublica_query->member_gender = "female"; break;
            case "N":
                $this->propublica_query->member_gender = "non-binary";

        }
    }
    public function create_congressional_civi_record()
    {
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_params =array(
            'contact_type' => "Individual",
            'contact_sub_type' => "Congressional_Representative",
            'first_name' => $this->propublica_query->get_member_first_name(),
            'middle_name' => $this->propublica_query->get_member_middle_name(),
            'last_name' => $this->propublica_query->get_member_last_name(),
            'birth_date' => $this->propublica_query->get_member_d_o_b(),
            'gender_id' =>  $this->propublica_query->get_member_gender(),
        );

        $this->civi_query->civi_query();

        $this->drupal_civicrm_id = $this->civi_query->get_civicrm_result()['id'];
    }
    public function Phone_number()
    {
       if($this->propublica_query->get_member_phone_number()){
        $phone = $res = preg_replace("/[^0-9]/", "", $this->propublica_query->get_member_phone_number() );
        $this->civi_query->civi_entity = "Phone";
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_params = array(
            'sequential' => 1,
            'contact_id' =>  $this->get_drupal_civicrm_id(),
            'phone_numeric' => $phone,
        );
        $this->civi_query->civi_query();
        if($this->civi_query->get_civicrm_result()['count'] < '1')
        {$this->create_phone();}}
    }
    public function create_phone()
    {
        $this->civi_query->civi_entity = "Phone";
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_params = array(
            'contact_id' => $this->get_drupal_civicrm_id(),
            'phone' => $this->propublica_query->get_member_phone_number(),
            'location_type_id' => "Work",
        );
        $this->civi_query->civi_query();
    }
    public function active_inactive()
    {
        if($this->propublica_query->get_member_active() == "true")
        {
            $this->activate_record();
        }
        else {
            $this->deactivate_record();
        }
    }
    public function get_title_for_record()
    {
        if ($this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() != "DC"
            && $this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() != "PR") {
                    $title = "Representative";
                } elseif ($this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() == "PR") {
                    $title = "Resident Commissioner";
                } elseif ($this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() != "DC") {
                    $title = "Delegate";
                } else {
                    $title = "Senator";
                }
                return $title;
            }
    public function activate_record()
    {
        $this->civi_query->civi_entity ="Contact";
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_params = array(
            'id' => $this->get_drupal_civicrm_id(),
            'contact_sub_type' => "Congressional_Representative",
            'formal_title' => $this->get_title_for_record(),
        );

        $this->civi_query->civi_query();
    }
    public function deactivate_record()
    {
        $this->civi_query->civi_entity ="Contact";
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_params = array(
            'id' => $this->get_drupal_civicrm_id(),
            'contact_sub_type' => "",
            'formal_title' => $this->get_title_for_record(),
        );
        $this->civi_query->civi_query();
    }
    public function database_process(){
        $member_id = null;
        $this->database = new base();
        $query = "select * from nfb_washington_members;";
        $key = 'propublica_id';
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $member)
        {

            $member = get_object_vars($member);
            if(strtolower(trim($member['propublica_id'])) == strtolower(trim($this->propublica_query->get_member_pp_id())))
            {
                $member_id = $member['member_id'];
            }
        }
        \Drupal::logger("nfb_washington_member_deduplication")->notice("Member Id: ".$member_id);
        if($member_id != null)
        {
            $this->update_member_record($member_id);
        }
        else{
            $this->insert_new_member();
        }
    }
    public function removal_run_through()
    {

        foreach($this->propublica_query->get_propublica_result()['results']['0']['members'] as $member)
        \Drupal::logger("nfb_washington_maintenance")->notice("member_array: ".print_r($member, true));
        {$this->propublica_query->leaving_congress_parse($member);
            if($this->propublica_query->get_member_state()  != "GU" &&
                $this->propublica_query->get_member_state()  != "AS"  &&
                $this->propublica_query->get_member_state()  != "VI"  &&
                $this->propublica_query->get_member_state()  != "MP" &&
                $this->propublica_query->get_member_state()  != "UM" &&
                $this->propublica_query->get_member_state() != "FM"&&
                $this->propublica_query->get_member_state() != "MH" &&
                $this->propublica_query->get_member_state() != "PW"
            )
            {
                $this->find_civi_record();
                $this->maintnence_database();
            }
            }
    }
    public function find_civi_record()
    {
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_params = array(
            'sequential' => 1,
            'contact_sub_type' => "Congressional_Representative",
            'first_name' => $this->propublica_query->get_member_first_name(),
            'last_name' => $this->propublica_query->get_member_last_name(),
        );
        $this->civi_query->civi_query();
        if($this->civi_query->get_civicrm_result()['count'] > 0)
        {
            $this->drupal_civicrm_id = $this->civi_query->get_civicrm_result()['values']['0']['contact_id'];
            $this->deactivate_record();
            $this->maintnence_database();
        }
    }
    public function  maintnence_database()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_members ;";
        $key = 'propublica_id';
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $member)
        {
            $member = get_object_vars($member);
            \Drupal::logger("nfb_washignton_debug")->notice("Results from sql: ".print_r($member, true));

            if($member['propublica_id'] == $this->propublica_query->get_member_pp_id())
            {$member_id = $member['member_id'];}
        }
        if($member_id)
        {$this->deactivate_maintnence_record($member_id);}
    }
    public function  update_member_record($member_id)
    {
        $this->database = new base();
        $query = "update nfb_washington_members
        set state = '".$this->propublica_query->get_member_state()."'
        where member_id = '".$member_id."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_members
        set civicrm_contact_id = '".$this->get_drupal_civicrm_id()."'
        where member_id = '".$member_id."';";
        $this->database->update_query($query);
        $this->convert_in_office();
        $query = "update nfb_washington_members
        set active = '".$this->propublica_query->get_member_active()."'
        where member_id = '".$member_id."';";
        $this->database->update_query($query);
        if($this->propublica_query->get_search_criteria_1() == "senate")
        {
            $query = "update nfb_washington_members
        set district = 'Senate'
        where member_id = '".$member_id."';";
            $this->database->update_query($query);
            $query = "update nfb_washington_members
        set rank = '".$this->propublica_query->get_member_rank()."'
        where member_id = '".$member_id."';";
            $this->database->update_query($query);
        }
        else {
            $query = "update nfb_washington_members
        set district = '".$this->propublica_query->get_member_district()."'
        where member_id = '".$member_id."';";
            $this->database->update_query($query);
            $query = "update nfb_washington_members
        set rank = 'House'
        where member_id = '".$member_id."';";
            $this->database->update_query($query);

        }

    }

    public function convert_in_office()
    {
        if($this->propublica_query->get_member_active() == "true")
        {$this->propublica_query->member_active = "0";}
        else {$this->propublica_query->member_active = "1";}
    }
    public function insert_new_member()
    {
        $this->convert_in_office();
        if($this->propublica_query->search_criteria_1 == "house")
        {  $fields = array(
            "civicrm_contact_id" => $this->get_drupal_civicrm_id(),
            "district" => $this->propublica_query->get_member_district(),
            "rank" => "House",
            "active" => $this->propublica_query->get_member_active(),
            "state" => $this->propublica_query->get_member_state(),
            "propublica_id" => $this->propublica_query->get_member_pp_id()
        );}
        else {
            $fields = array(
                "civicrm_contact_id" => $this->get_drupal_civicrm_id(),
                "district" => "Senate",
                "rank" => $this->propublica_query->get_member_rank(),
                "active" => $this->propublica_query->get_member_active(),
                "state" => $this->propublica_query->get_member_state(),
                "propublica_id" => $this->propublica_query->get_member_pp_id());
        }
        $table = "nfb_washington_members";
        $this->database = new base();
        $this->database->insert_query($table, $fields);
    }
    public function deactivate_maintnence_record($member_id)
    {
        $query = "update nfb_washington_members
        set active = '0'
        where member_id = '".$member_id."';";
        $this->database->update_query($query);
    }



}