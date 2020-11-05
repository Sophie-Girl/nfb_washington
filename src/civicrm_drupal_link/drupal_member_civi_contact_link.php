<?php
Namespace Drupal\nfb_washington\civicrm_drupal_link;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\propublica\members;

class drupal_member_civi_contact_link
{
    public $civi_query;
    public $propublica_query;
    public $drupal_civicrm_id;
    public function  get_drupal_civicrm_id()
    {return $this->drupal_civicrm_id;}
    public function  __construct(civi_query $civi_query, members $members)
    {
        $this->civi_query = $civi_query;
        $this->propublica_query = $members;
    }
    public function set_up_general_member_process()
    {
        $this->propublica_query->set_api_key();
        $this->propublica_query->set_congress_number();
        $this->propublica_query->entity = "members";
        $this->propublica_query->search_criteria_1 = "house";
        $this->propublica_query->member_query();
        $this->general_run_through();
        $this->propublica_query->search_criteria_1 = "senate";
        $this->propublica_query->member_query();
        $this->general_run_through();
    }
    public function set_up_old_congress_maintenance_query()
    {
        $this->propublica_query->set_api_key();
        $this->propublica_query->set_congress_number();
        $this->propublica_query->congress_number = (int)$this->propublica_query->get_congress_number() - 1;
        $this->propublica_query->member_query();
    }
    public function general_run_through()
    {
        \drupal::logger("nfb_Washington")->notice(print_r($this->propublica_query->get_propublica_result(), true));
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
           \Drupal::logger("nfb_Washingotn")->notice("I found the contact. I'm adding gender");
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
        \Drupal::logger("nfb_washington_address")->notice("get_Address".print_r($this->civi_query->get_civicrm_result(), true));
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
        \drupal::logger("nfb_Washington")->notice("update_contact_record");
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
            CAse "N":
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
        \drupal::logger("nfb_Washington")->notice("create_contact_record: ". $this->civi_query->get_civicrm_result()['id']." ".$this->get_drupal_civicrm_id());
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
        \drupal::logger("nfb_Washington")->notice("phone_record");
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
        if($this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() != "DC"
            && $this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() != "PR")
        {$title = "Representative";}
        elseif($this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() == "PR")
        {$title = "Resident Commissioner";}
        elseif($this->propublica_query->get_search_criteria_1() == "house" && $this->propublica_query->get_member_state() != "DC")
        {$title = "Delegate";}
        else {$title = "Senator";}
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
        \drupal::logger("nfb_Washington")->notice("activate_record ".print_r($this->civi_query->get_civicrm_params(), true));
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
        \drupal::logger("nfb_Washington")->notice("deactivate_record");
        $this->civi_query->civi_query();
    }
    public function database_process(){

    }





}