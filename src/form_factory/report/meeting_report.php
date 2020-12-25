<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\database\base;

class meeting_report
{
    public $database;
    public $civicrm;
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
    public $created_user;
    public function  get_created_user()
    {return $this->created_user;}
    public $modified_user;
    public function get_modified_user()
    {return $this->modified_user;}
    public $markup;
    public function get_markup()
    {return $this->markup;}
    public function build_form(&$form, $form_state)
    {
        $form['file_type'] = array(
            '#type' => 'select',
            '#title' => "Select file type for download",
            '#options' => array(
                'docx' => "Word Doc for braille production",
                'csv' => "excel Spreadsheet",
            ),
            '#required' => true,
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );
    }



    Public Function build_markup(FormStateInterface $form_state)
    {
        $this->set_state($form_state);
        if($form_state->getValue("state_select") == "")
        {$markup = "<p> Select a state to view a preview of the report</p>";}
        else{
            $this->set_state($form_state);
            $this->start_webpage_markup();
            $markup = $this->get_markup();
        }
        return $markup;
    }
    public function backend_text_builder(FormStateInterface $form_state)
    {
        if($form_state->getValue("doc_type") == "full")
        { $this->full_member_query();
        $this->start_full_download_markup();}
        else{
            $this->set_state($form_state);
            $this->member_query();
            $this->start_state_download_markup();

        }
    }
    public function set_state(FormStateInterface $form_state)
    {
        $this->state = $form_state->getValue("state_select");
    }

    public function full_member_query()
    {
        $this->database = new base();
        $query = "Select * from nfb_washington_members where active = 0 order by state ASC ;";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->member_results = $this->database->get_result();
        $this->database = null;
        $member_array = [];
        foreach( $this->get_member_results() as $member)
        {
            $this->set_member_values($member);
            $this->meeting_query($member_array);
        }
        $this->member_results = $member_array;
        $this->start_full_download_markup();
    }

    public function start_full_download_markup()
    {
        $year = date('Y');
        $this->markup = "Washington Seminar ".$year." Meetings Report".PHP_EOL;
        $this->download_markup();
    }
    public function set_member_values($member)
    {
        $member= get_object_vars($member);
        $this->member_id = $member['member_id'];
        $this->civi_id = $member['civicrm_contact_id'];
        $this->district = $member['district'];
        $this->state = $member['state'];
        $this->rank = $member['rank'];
        $this->civi_query_stuff();
    }
    public function civi_query_stuff()
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civicrm = new civi_query($civi);
        $this->civicrm->civi_entity = "Contact";
        $this->civicrm->civi_mode = 'get';
        $this->civicrm->civi_params = array(
            'sequential' => 1,
            'id' => $this->get_civicrm_id(),
        );
        $this->civicrm->civi_query();
        foreach($this->civicrm->get_civicrm_result()['values'] as $contact)
        {
            $this->first_name = $contact['first_name'];
            $this->last_name =  $contact['last_name'];
            $this->phone = $contact['phone'];
        }
        $this->civicrm = null;
    }
    public function meeting_query(&$member_array)
    {
        $this->database = new base(); $year = date('Y');
        $query = "select * from nfb_washington_activities where meeting_year = '".$year."'
        and member_id = '".$this->get_member_id()."' and type  = 'meeting';";

        $key = 'activity_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $meeting)
        {
            $meeting = get_object_vars($meeting);

            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['location']= $meeting['location'];
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['date'] = $meeting['meeting_date'];
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['time'] = $meeting['meeting_time'];
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['nfb_contact'] = $meeting['nfb_contact'];
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['nfb_phone'] = $meeting['nfb_phone'];
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['moc_contact'] = $meeting['m_o_c_contact'];
            $attendance = $meeting['moc_attendance'];
            $this->convert_attendance($attendance);
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['moc_attendance'] = $attendance
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['created_user'] = $meeting['created_user'];
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['modified_user'] = $meeting['last_modified_user'];
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['first_name'] = $this->get_first_name();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['last_name'] = $this->get_last_name();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['phone'] = $this->get_phone();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['rank'] = $this->get_rank();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['district'] = $this->get_rank();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['state'] = $this->get_state();

        }
        if($this->database->get_result() == [])
        {

            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['location']= "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['date'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['time'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['nfb_contact'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['nfb_phone'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['moc_contact'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['moc_attendance'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['created_user'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['modified_user'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['first_name'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['last_name'] = "Unknown";
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['phone'] = $this->get_phone();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['rank'] = $this->get_rank();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['district'] = $this->get_rank();
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['state'] = $this->get_state();
        }
        $this->database = null;
    }
    public function convert_attendance(&$attendance)
    {
        if($attendance == "0")
        {
            $attendance = "No";
        }
        else
        {$attendance = "Yes";}
    }

    public function download_markup()
    {
        $this->markup = $this->get_markup()."---------------------------------------------------------------------".PHP_EOL;
        foreach($this->get_member_results() as $member)
        {
            \drupal::logger("wft")->notice("member: ".print_r($member, true));
            $this->markup = $this->get_markup(). $member['first_name']." ".$member['last_name'].PHP_EOL.
                $this->district_text($member). " Phone number: ".$member['phone'].PHP_EOL.
                "Zoom Meeting ID: ".$member['location']." Meeting date: ".$member['date'].PHP_EOL.
                "Meeting Time: ". $member['time']. PHP_EOL.
                "NFB Contact: ".$member['nfb_contact']. " Phone: ".$member['nfb_phone']. PHP_EOL.
                "Attending Meeting: ".$member['moc_attendance']. " MOC Contact: ". $member['moc_contact'].PHP_EOL.
                "---------------------------------------------------------------------".PHP_EOL;
        }
    }
    public function district_text($member)
    {
        if($member['district'] == "Senate")
        { $district_text = strtoUpper(substr($member['rank'], 0,1)).substr($member['rank'], 1, 12). " Senator from "
        . $member['state'];}
        elseif($member['state'] == "DC")
        {$district_text = "Delegate for ".$member['state'];}
        elseif($member['state'] == "PR")
        {$district_text = "Resident Commissioner for ".$member['state'];}
        else {$district_text = "Representative for ".$member['state']." District: ".$member['district'];}
        return $district_text;
    }


}