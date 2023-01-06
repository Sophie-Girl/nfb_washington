<?php
Namespace Drupal\nfb_washington\civicrm_drupal_link;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\propublica\members;

class drupal_member_civi_contact_link
{
    public $civi_query;
    public $propublica_query;
    public $drupal_civicrm_id;
    public $database;
    public $relationship_id;
    public function  get_drupal_civicrm_id()
    {return $this->drupal_civicrm_id;}
    public function get_relationsihp_id()
    {
        return $this->relationship_id;
    }
    public function  __construct(civicrm_v4 $civi_query, members $members)
    {
        $this->civi_query = $civi_query;
        $this->propublica_query = $members;
    }
    public function new_congress_run_through()
    {
        $this->set_up_general_member_process();
        $this->set_up_old_congress_maintenance_query();
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
        $this->propublica_query->entity = "members";
        $this->propublica_query->search_criteria_1 = "house";
        $this->propublica_query->congress_number = (int)$this->propublica_query->get_congress_number() - 1;
        $this->propublica_query->leaving_congress_query();
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
        $this->deduplication_v4();
       // $this->address_and_phone(); API version 3 no longer viable
        $this->address_and_phone_v4();
        $this->active_inactive();
        $this->party_functions();
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
    public function deduplication_v4()
    {
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_params = [
          'select' => [
        '*',
    ],
  'where' => [
        ['contact_sub_type', '=', 'Congressional_Representative'],
        ['first_name', '=', $this->propublica_query->get_member_first_name()],
        ['last_name', '=', $this->propublica_query->get_member_last_name()],
    ],
  'limit' => 25,];
        $result = $this->civi_query->civi_query_v4();
        $count = $result->count();
        if($count > 0)
        {
            $contact = $result->first();
            $this->drupal_civicrm_id =  $contact['id'];
            $this->convert_gender();
            $this->update_congressional_details_v4();

        }
        else {
            $this->convert_gender();
            $this->create_congressional_civi_record_v4();
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
     //   $this->Phone_number(); API 3 no longer supported
        $this->Phone_number_v4();
    }
    public function address_and_phone_v4()
    {
        $this->civi_query->civi_entity = "Address";
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_params = [
            'select' => [
                '*',
            ],
            'where' => [
                ['contact_id', '=', $this->get_drupal_civicrm_id()],
                ['state_province_id', '=', 1050],
                ['city', '=', 'Washington'],
            ],
            'limit' => 25,
        ];
        $result = $this->civi_query->civi_query_v4();
        $count = $result->count();
        if($count > 0)
        {
            $current = 0; $address_id = null;
            while ($current <= $count){
                $address =  $result->itemAt($current);
                if($address_id == null)
                {
                    $address_id = $address['id'];
                    $this->update_address_v4($address_id);
                }
                $current++;
            }
        }
        else{
            $this->create_address_v4();
        }
        //   $this->Phone_number(); API 3 no longer supported
        $this->Phone_number_v4();
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

    public function update_address_v4($address_id)
    {
        $this->civi_query->civi_mode = "update";
        $this->civi_query->civi_entity = "Address";
        $this->civi_query->civi_params =
        [
        'values' => [
        'street_adress' => $this->propublica_query->get_office_address(),
    ],
  'where' => [
        ['id', '=', $address_id],
    ],
];
        $result = $this->civi_query->civi_query_v4();
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
    public function create_address_v4()
    {
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_entity = "Address";
        $this->civi_query->civi_params = [
          'values' => [
        'contact_id' => $this->get_drupal_civicrm_id(),
        'street_address' => $this->propublica_query->get_office_address(),
        'location_type_id' => 2,
        'state_province_id' => 1050,
        'country_id' => 1228,
        'city' => 'Washington',
    ], ];

        $result = $this->civi_query->civi_query_v4();

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
    public function  update_congressional_details_v4()
    {
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = "update";
        $this->civi_query->civi_params =
        [
        'values' => [
        'first_name' => $this->propublica_query->get_member_first_name(),
        'last_name' => $this->propublica_query->get_member_last_name(),
        'middle_name' => $this->propublica_query->get_member_middle_name(),
        'birth_date' => $this->propublica_query->get_member_d_o_b(),
        'gender_id:name' => $this->propublica_query->get_member_gender(),
    ],
  'where' => [
        ['id', '=', $this->get_drupal_civicrm_id()],
    ],];
      $result =  $this->civi_query->civi_query_v4();
    }
    public function convert_gender()
    {
        switch ($this->propublica_query->get_member_gender())
        {
            case "M":
                $this->propublica_query->member_gender = "Male"; break;
            case "F":
                $this->propublica_query->member_gender = "Female"; break;
            case "N":
                $this->propublica_query->member_gender = "Nonbinary"; break;
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
    public function create_congressional_civi_record_v4()
    {
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_params =[
            'values' => [
        'contact_type' => 'Individual',
        'contact_sub_type' => ['Congressional_Representative'],
        'first_name' => $this->propublica_query->get_member_first_name(),
        'middle_name' => $this->propublica_query->get_member_middle_name(),
        'last_name' => $this->propublica_query->get_member_last_name(),
        'birth_date' => $this->propublica_query->get_member_d_o_b(),
        'gender_id:name' => $this->propublica_query->get_member_gender(),
    ]
        ];
        $result = $this->civi_query->civi_query_v4(); // convert to api v4
        $count = $result->count(); $new_c = $result->first();
        $this->drupal_civicrm_id = $new_c['id'];
    }
    public function Phone_number()
    {
       if($this->propublica_query->get_member_phone_number()){
        $phone =  preg_replace("/[^0-9]/", "", $this->propublica_query->get_member_phone_number() );
        $this->civi_query->civi_entity = "Phone";
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_params = array(
            'sequential' => 1,
            'contact_id' =>  $this->get_drupal_civicrm_id(),
            'phone_numeric' => $phone,
        );
        $this->civi_query->civi_query();
        if($this->civi_query->get_civicrm_result()['count'] < '1')
        {
        //    $this->create_phone();
        $this->create_phone_v4();
        }
       }
    }
    public function Phone_number_v4()
    {
        if($this->propublica_query->get_member_phone_number()){
            $phone =  preg_replace("/[^0-9]/", "", $this->propublica_query->get_member_phone_number() );
            $this->civi_query->civi_entity = "Phone";
            $this->civi_query->civi_mode = "get";
            $this->civi_query->civi_params = array(
                'select' => [
                    '*',
                ],
                'where' => [
                    ['contact_id', '=', $this->get_drupal_civicrm_id()],
                    ['phone_numeric', '=', $phone],
                ],
                'limit' => 25,
            );
            $result = $this->civi_query->civi_query_v4();
            $count = $result->count();

            if($count < '1') //check if that's good? Need ot see result structure in a print_r to confirm how much has changed
            {$this->create_phone_v4();}}
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
    public function create_phone_v4()
    {
        $this->civi_query->civi_entity = "Phone";
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_params = array(
            'values' => [
                'contact_id' => $this->get_drupal_civicrm_id(),
                'phone' => $this->propublica_query->get_member_phone_number(),
                'location_type_id' => '2',
            ],
        );
        $result = $this->civi_query->civi_query_v4();
    }
    public function active_inactive()
    {
        if($this->propublica_query->get_member_active() == "true")
        {
           // $this->activate_record(); deprecated API 3 call
            $this->activate_record_v4();
        }
        else {
         //   $this->deactivate_record(); deprecated API 3 call
            $this->deactivate_record_v4();
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
    public function activate_record_v4()
    {
        $this->civi_query->civi_entity ="Contact";
        $this->civi_query->civi_mode = "update";
        $this->civi_query->civi_params = array(
            'where' => [
                ['id', '=', $this->get_drupal_civicrm_id()],
            ],
            'values' => [
                'contact_sub_type' => ['Congressional_Representative'],
                'formal_title' => $this->get_title_for_record(),
            ],
            'limit' => 1,
        );

       $result = $this->civi_query->civi_query_v4();
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
    public function deactivate_record_v4()
    {
        $this->civi_query->civi_entity ="Contact";
        $this->civi_query->civi_mode = "update";
        $this->civi_query->civi_params = array(
            'where' => [
                ['id', '=', $this->get_drupal_civicrm_id()],],
                'values' => [
                'contact_sub_type' => [],
            'formal_title' => $this->get_title_for_record(),
                    ],
            'limit' => 1,
        );
        $result = $this->civi_query->civi_query_v4();
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
        {
        $this->propublica_query->leaving_congress_parse($member);
                $this->find_civi_record_v4();
                $this->maintnence_database();}
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
           // $this->deactivate_record();
            $this->deactivate_record_v4(); // new version for API updates
            $this->maintnence_database();
        }
    }
    public function find_civi_record_v4()
    {
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_params =
        [
        'select' => [
        '*',
    ],
  'where' => [
        ['contact_sub_type', '=', 'Congressional_Representative'],
        ['first_name', '=', $this->propublica_query->get_member_first_name()],
        ['last_name', '=', $this->propublica_query->get_member_last_name()],
    ],
  'limit' => 25,
]
        ;
        $result = $this->civi_query->civi_query_v4();
        $count = $result->count();
        if($count > 0)
        {
            $contact = $result->first();
            $this->drupal_civicrm_id = $contact['id'];
            // $this->deactivate_record();
            $this->deactivate_record_v4(); // new version for API updates
            $this->maintnence_database();
        }
    }
    public function  maintnence_database()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_members where propublica_id = '".$this->propublica_query->get_member_pp_id()."';";
        $key = 'propublica_id';
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $member)
        {
            $member = get_object_vars($member);
            $member_id = $member['member_id'];
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
        set district = '"."Senate"."'
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
            $member_id = str_replace('"', "", $member_id);
            $query = "update nfb_washington_members
        set rank = '"."House"."'
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
        set active = '1'
        where member_id = '".$member_id."';";
        $this->database->update_query($query);
    }
    public function party_functions()
    {
        $this->relationship_id = null;
        $p_name = $this->party_name_switch();
        $party_c_id = $this->find_party_c_id($p_name);
        $this->check_for_member_ships($party_c_id);
    }
    public function party_name_switch()
    {
        switch ($this->propublica_query->get_member_party())
        {
            case "R":
                $p_name = "Republican Party";
                break;
            case "D":
                $p_name = "Democratic Party";
                break;
            case "ID":
                $p_name = "Independent";
                break;
            case "IR":
                $p_name = "Independent";
                break;
            case "I":
                $p_name = "Independent";
                break;
        }
        return $p_name;
    }
    public function find_party_c_id($p_name)
    {
        $party_c_id = null;
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_params = [
            'select' => [
                '*',
            ],
            'where' => [
                ['organization_name', '=', $p_name],
                ['contact_sub_type', '=', 'Political_Party'],
            ],
            'limit' => 25,
        ];
       $result = $this->civi_query->civi_query_v4();
       $count = $result->count(); $current = 0;
       while($current <= $count)
       {
           $party =  $result->itemAt($current);
           if($party_c_id == null)
           {
               $party_c_id = $party['id'];
           }
           $current++;
       }
       return $party_c_id;
    }
    public function get_relate_type()
    {
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_entity = "RelationshipType";$this->civi_query->civi_params = [
            'select' => [
                '*',
            ],
            'where' => [
                ['name_a_b', '=', 'Member_of'],
            ],
            'limit' => 25,
        ];
        $result = $this->civi_query->civi_query_v4();
        $result = $result->first();
        return $result['id'];

    }
    public function check_for_member_ships(&$party_c_id)
    {
        $type = $this->get_relate_type();
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_entity = "Relationship";
        $this->civi_query->civi_params = [
            'select' => [
            '*',
        ],
  'where' => [
        ['relationship_type_id', '=', $type],
        ['contact_id_b', '=', $this->get_drupal_civicrm_id()],
    ],
  'limit' => 25,
];
        $result = $this->civi_query->civi_query_v4();
        $count = $result->count();
        if($count != 0)
        {
            $relat = $result->first();
            $this->relationship_id = $relat['id'];
        }
        else {
            $this->relationship_id = null;
        }
        $this->update_create_relationship($party_c_id, $type);
    }
    public function update_create_relationship(&$party_c_id, $type)
    {
        if($this->get_relationsihp_id() == null)
        {
            $this->create_relate_params($party_c_id, $type);
        }
        else{
            $this->update_existing_relationship($party_c_id);
        }
    }
    public function create_relate_params($party_c_id, $type)
    {
        $this->civi_query->civi_mode = "create";
        $this->civi_query->civi_entity = "Relationship";
        $this->civi_query->civi_params =
            [
                'values' => [
                    'contact_id_a' => $party_c_id,
                    'contact_id_b' => $this->get_drupal_civicrm_id(),
                    'relationship_type_id' => $type,
                    'is_active' => TRUE,
                ],
            ];
        $result = $this->civi_query->civi_query_v4();
    }
    public function update_existing_relationship($party_c_id)
    {
        $this->civi_query->civi_mode = "update";
        $this->civi_query->civi_entity = "Relationship";
        $this->civi_query->civi_params = [
            'values' => [
                'contact_id_a' => $party_c_id,
                'is_active' => TRUE,
            ],
            'where' => [
                ['id', '=', ''],
            ],
        ];
        $result = $this->civi_query->civi_query_v4();

    }


}