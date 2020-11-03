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
    {return $this>$this->drupal_civicrm_id;}
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
        foreach($this->propublica_query->get_propublica_result() as $member)
        {$this->propublica_query->parse_member($member);}
    }
    public function civi_processes(){
        $this->deduplication();
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
        $this->civi_query->civi_query();;
        $this->drupal_civicrm_id = $this->civi_query->get_civicrm_result()['id'];
    }




}