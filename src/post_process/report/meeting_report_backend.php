<?php
namespace  Drupal\nfb_washington\post_process\report;
use Drupal\civicrm\Civicrm; // V3 will become deprecated.
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\database\base;
class meeting_report_backend extends all_member_download
{
    /* ################################################################
     * # Connell, Sophia: Code Clean up cause I was rushed af on this #
     * # First file to use CiviAPI version 4.                         #
     * ################################################################  */
    public $database;
    public $civicrm;
    public $null_filter;
    public function get_null_filter()
    {return $this->null_filter;}
    public $state_filter;
    public function get_state_filter()
    {return $this->state_filter;}
    public $member_results;
    public function get_member_results()
    {return $this->member_results;}
    public $member_id;
    public function get_member_id()
    {return $this->member_id;}
    public $civi_id;
    public function get_civicrm_id()
    {return $this->civi_id;}
    public $district;
    public function get_district()
    {return $this->district;}
    public $rank;
    public function get_rank()
    {return $this->rank;}
    public $state;
    public function get_state()
    {return $this->state;}
    public $first_name;
    public function get_first_name()
    {return $this->first_name;}
    public $last_name;
    public function get_last_name()
    {return $this->last_name;}
    public $phone;
    public function get_phone()
    {return $this->phone;}
    public $location;
    public function get_location()
    {return $this->location;}
    public $date;
    public function get_date()
    {return $this->date;}
    public $time;
    public function get_time()
    {return $this->time;}
    public $nfb_contact;
    public function get_nfb_contact()
    {return $this->nfb_contact;}
    public $nfb_phone;
    public function get_nfb_phone()
    {return $this->nfb_phone;}
    public $moc_contact;
    public function get_moc_contact()
    {return $this->moc_contact;}
    public $moc_attendance;
    public function get_moc_attendance()
    {return $this->moc_attendance;}
    public $markup;
    public function get_markup()
    {return $this->markup;}
    public $array;
    public function get_array()
    {return $this->array;}
    public $count;
    public function get_count()
    {return $this->count;}
    public $committee_text;
    public function get_committee_text()
    {return $this->committee_text;}

    public function full_member_query()
    {
        $this->database = new base();
        $query = "Select * from nfb_washington_members where active = 0 order by state ASC ;";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->member_results = $this->database->get_result();
        $this->database = null;
    }
    public function download_markup()
    {

        $this-> commitee_search();
     //   $this->markup = $this->get_markup()."Date: ".$this->get_date()." Time: ". $this->get_time()." ". $this->get_first_name()." ".$this->get_last_name()." ".$this->district_text()."-".$this->relationship_check_for_party().PHP_EOL.
        $this->markup = $this->get_markup()."Date: ".$this->get_date()." Time: ". $this->get_time()." ". $this->get_first_name()." ".$this->get_last_name()." ".$this->district_text()."-".$this->relationship_check_for_party().PHP_EOL.
            $this->get_committee_text().PHP_EOL.
            "Location: ".$this->get_location().PHP_EOL.
            "NFB Contact: ".$this->get_nfb_contact(). " Phone: ".$this->get_nfb_phone(). PHP_EOL.
             " MOC Contact: ". $this->get_moc_contact().PHP_EOL.
            "---------------------------------------------------------------------".PHP_EOL
    ;
        $count = $this->get_count();
        $this->count = $count++;

    }
    public function meeting_first_query()
    {

        $this->database = new base(); $year = date("Y");
        $query = "select * from nfb_washington_activities where meeting_year = '".$year."' 
        order by meeting_date ASC;";
        $key = 'activity_id';
        $this->database->select_query($query, $key);
        $this->member_results = $this->database->get_result();
        $this->database = null;


    }
    public function district_text()
    {
        if($this->get_district() == "Senate")
        { $district_text = strtoUpper(substr($this->get_rank(),0,1))."-". $this->get_state();}
        elseif($this->get_state() == "DC")
        {$district_text = "Delegate - ".$this->get_state();}
        elseif($this->get_state() == "PR")
        {$district_text = "Resident Commissioner - ".$this->get_state();}
        else {$district_text = $this->get_district()." - ".$this->get_state();}
        return $district_text;
    }
    public function begin_new_download_markup(FormStateInterface $form_state)
    {
        ini_set('max_execution_time', 500);
        $this->count = 1;
        if($form_state->getValue("filter_results") == "unscheduled")
        {$this->null_filter = "on";} else{$this->null_filter = "off";}
        if($form_state->getValue("filter_results") =="state")
        {$this->state_filter = $form_state->getValue("state_select");}
        else{$this->state_filter = "all";}
        $year = date("Y");
        if($form_state->getValue("file_type") == "docx"){
        $this->markup = $year." Washington Seminar Meetings Report".PHP_EOL.
            "------------------------------------------------------------------".PHP_EOL;}
        else {$this->start_array();}
        $this->meeting_first_query();
        $this->process_meeting_query($form_state);
    }
    public function process_meeting_query(FormStateInterface $form_state)
    {
       //
        foreach($this->get_member_results() as $meeting)
        {
            $meeting = get_object_vars($meeting);
            if($this->get_null_filter() == "on" && $meeting['location'] == "Unknown" )
            {
                $null_go = true;
            }
            elseif(   $this->get_null_filter() == "on" && $meeting['location'] == "TBD")
            {
                $null_go = true;
            }
            elseif($this->get_null_filter() == "off")
            {
                $null_go = true;
            }
            else{ $null_go = false;}
            if($null_go == true) {
                $this->clear_meeting();
                $this->member_id = $meeting['member_id'];
                $this->location = $meeting['location'];
                $this->date = $meeting['meeting_date'];
                $this->time = $meeting['meeting_time'];
                $this->nfb_contact = $meeting['nfb_contact'];
                $this->nfb_phone = $meeting['nfb_phone'];
                $this->moc_contact = $meeting['m_o_c_contact'];
                $this->moc_attendance = $meeting['moc_attendance'];
                $this->member_query_meeting_report($form_state);
            }
        }
    }
    public function clear_meeting()
    {
        $this->member_id = null;
        $this->location = null;
        $this->date = null;
        $this->time = null;
        $this->nfb_contact = null;
        $this->nfb_phone = null;
        $this->moc_contact = null;
        $this->moc_attendance = null;
    }
    public function member_query_meeting_report(FormStateInterface $form_state)
    {

        $this->database = new base();
        $query = "select * from nfb_washington_members where member_id = '".$this->get_member_id()."';";
        $key = "member_id";
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $member)
        {
            $member = get_object_vars($member);
            if($this->get_state_filter()== $member['state'])
            {$state_go = "go";}
            elseif($this->get_state_filter() == "all")
            {$state_go = "go";}
            else { $state_go = "no";}
            if($state_go == "go") {
                $this->state = $member['state'];
                $this->rank = $member['rank'];
                $this->district = $member['district'];
                $this->civi_id = $member['civicrm_contact_id'];
                $this->first_name = null;
                $this->civi_query_stuff();
            if($form_state->getValue("file_type") == "docx") {
                     $this->download_markup();}
            else{
                $this->build_array_row();
            }

            }
        }
    }
    public function civi_query_stuff()
    {
        $this->first_name = null;
        $this->civicrm->civi_entity = "Contact";
        $this->civicrm->civi_mode = 'get';
        $this->civicrm->civi_params = [
            'select' => [
                '*',
                'phone.*',
            ],
            'join' => [
                ['Phone AS phone', FALSE, NULL, ['phone.contact_id', '=', 'id']],
            ],
            'where' => [
                ['id', '=', $this->get_civicrm_id()],
                ['phone.is_primary', '=', TRUE],
                // So using primary is needed for just grabbing one contact
            ],
            'checkPermissions' => FALSE,
            'limit' => 1,
        ];
        $result = $this->civicrm->civi_query_v4();
        $count = $result->count(); $current = 0;
        while($count > $current)
        {
            $contact = $result->itemAt($current);
            if($this->first_name == null){
            $this->first_name = $contact['first_name'];
            $this->last_name =  $contact['last_name'];
            $this->phone = $contact['phone.phone'];}
            $current++;
        }
    }
    public function start_array()
    {
        $data['0']['first_name'] = "First_Name";
        $data['0']['last_name'] = "Last_Name";
        $data['0']['phone'] = "Office_Phone";
        $data['0']['district_text'] = "District/Senate_Rank";
        $data['0']['state'] = "State";
        $data['0']['location'] = "Location";
        $data['0']['time'] = "Time";
        $data['0']['date'] = "Date";
        $data['0']['nfb_contact'] = "NFB Contact";
        $data['0']['nfb_phone'] = "NFB Phone";
        $data['0']['attending'] = "Attending";
        $data['0']['congressional_contact'] = "Congressional Contact";
        $this->array = $data;
    }
    public function build_array_row()
    {
        $data =  $this->get_array();
        $data[$this->get_count()]['first_name'] = $this->get_first_name();
        $data[$this->get_count()]['last_name'] = $this->get_last_name();
        $data[$this->get_count()]['phone'] = $this->get_phone();
        if($this->get_rank()){
        $data[$this->get_count()]['district_text'] = $this->get_rank();}
        else {$data[$this->get_count()]['district_text'] = $this->get_district();}
        $data[$this->get_count()]['state'] = $this->get_state();
        $data[$this->get_count()]['location'] = $this->get_location();
        $data[$this->get_count()]['time'] = $this->get_time();
        $data[$this->get_count()]['date'] = $this->get_date();
        $data[$this->get_count()]['nfb_contact'] = $this->get_nfb_contact();
        $data[$this->get_count()]['nfb_phone'] = $this->get_nfb_phone();
        $data[$this->get_count()]['attending'] = $this->get_moc_attendance();
        $data[$this->get_count()]['congressional_contact'] = $this->get_moc_contact();
        $this->array = $data;
        $count = $this->get_count(); $count++;
        $this->count = $count;
    }
    public function create_committe_text()
    {
        $this->committee_text = "";

    }
    public function commitee_search()
    {
        $this->form_factory->member_id = $this->get_member_id();
        $this->form_factory->civicrm_id = $this->get_civicrm_id();
        $this->committee_text_maker();
        $this->handle_notes();
        $this->committee_text = $this->get_committee_markup().PHP_EOL.$this->get_note_text();
    }
    public function relationship_check_for_party()
    {
        $this->civicrm->civi_mode = "get";
        $this->civicrm->civi_entity = "Relationship";
        $this->civicrm->civi_params = [
            'select' => [
                '*',
            ],
            'where' => [
                ['contact_id_b', '=', $this->get_civicrm_id()],
                ['relationship_type_id', '=', 55],
            ],
            'checkPermissions' => FALSE,
            'limit' => 25,
        ];
        $result = $this->civicrm->civi_query_v4();
        $relat = $result->first();
        $party_id = $relat['contact_id_a'];
        $name  = $this->get_party_dispaly_name($party_id);
        Return substr($name,0,1);

    }
    public function get_party_dispaly_name($party_id)
    {
        $this->civicrm->civi_entity = "Contact";
        $this->civicrm->civi_mode = 'get';
        $this->civicrm->civi_params =  [
            'select' => [
                '*',
            ],
            'where' => [
                ['id', '=', $party_id],
            ],
            'limit' => 25,
        ];
        $result = $this->civicrm->civi_query_v4();
        $party = $result->first();
        return $party['display_name'];
    }





}