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
    public function member_query()
    {
        $this->database = new base();
        $query = "Select * from nfb_washington_members where state = '".$this->get_state()."'
        and active = 0 ;";
        $key = 'member_id';

        $this->database->select_query($query, $key);
        $this->member_results = $this->database->get_result();
        $this->database = null;
    }
    public function full_member_query()
    {
        $this->database = new base();
        $query = "Select * from nfb_washington_members where active = 0 order by state ASC ;";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->member_results = $this->database->get_result();
        $this->database = null;
    }
    public function start_webpage_markup()
    {
        $this->markup = "<h2>".$this->get_state()." Meeting Report</h2>";
        $this->web_markup_builder();
    }
    public function start_full_download_markup()
    {
        $year = date('Y');
        $this->markup = "Washington Seminar ".$year." Meetings Report".PHP_EOL;
        $this->download_markup();
    }
    public function start_state_download_markup()
    {
        $year = date('Y');
        $this->markup = "Washington seminar ".$year." ".$this->get_state()." Meetings Report".PHP_EOL;
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
        $this->meeting_query();
        $this->convert_attendance();
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
    public function meeting_query()
    {
        $this->database = new base(); $year = date('Y');
        $query = "select * from nfb_washington_activities where meeting_year = '".$year."'
        and member_id = '".$this->get_member_id()."' and type  = 'meeting';";

        $key = 'activity_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $meeting)
        {
            $meeting = get_object_vars($meeting);

            $this->location = $meeting['location'];
            $this->date = $meeting['meeting_date'];
            $this->time = $meeting['meeting_time'];
            $this->nfb_contact = $meeting['nfb_contact'];
            $this->nfb_phone = $meeting['nfb_phone'];
            $this->moc_contact = $meeting['m_o_c_contact'];
            $this->moc_attendance = $meeting['moc_attendance'];
            $this->created_user = $meeting['created_user'];
            $this->modified_user = $meeting['last_modified_user'];
        }
        if($this->database->get_result() == [])
        {

            $this->location = "Unknown";
            $this->date = "Unknown";
            $this->time = "Unknown";
            $this->nfb_contact = "Unknown";
            $this->nfb_phone = "Unknown";
            $this->moc_contact = "Unknown";
            $this->moc_attendance = "Unknown";
            $this->created_user = "Unknown";
            $this->modified_user = "Unknown";
        }
        $this->database = null;
    }
    public function convert_attendance()
    {
        if($this->get_moc_attendance() == "0")
        {
            $this->moc_attendance = "No";
        }
        else
        {$this->moc_attendance = "Yes";}
    }
    public function web_markup_builder()
    {
        $this->member_query();
        $this->markup = $this->get_markup().
        "<table><tr><th class='table-header'>Member of Congress Name:</th><th class='table-header'> Member of Congress Phone:</th><th class='table-header'>District/Senate Rank</th><th class ='table-header'>
        Zoom Meeting ID:</th><th class='table-header'> Meeting Time:</th> <th class='table-header'>NFB Contact Information:
        </th><th class='table-header'>MOC Attendance</th><th class='table-header'> MOC Contact</th></tr>";
       \drupal::logger("nfb_washignton__markup")->notice($this->get_markup());
        foreach($this->get_member_results() as $member)
        {
            $this->set_member_values($member);
            $this->markup = $this->get_markup(). "<tr><td>".$this->get_first_name()." ".$this->get_last_name()."</td>
<td>".$this->get_phone()."</td><td>".$this->district_text()."</td><td>".$this->get_location()."</td><td>".$this->get_date()." ".$this->get_time()."</td>
<td>".$this->get_nfb_contact()." Phone: ".$this->get_nfb_phone()."</td><td>".$this->get_moc_attendance()."</td><td>".$this->get_moc_contact()."</td></tr>";
            \drupal::logger("nfb_washington_markup")->notice($this->get_markup());
        }
        $this->markup = $this->get_markup(). "</table>";


    }
    public function download_markup()
    {
        $this->markup = $this->get_markup()."---------------------------------------------------------------------".PHP_EOL;
        foreach($this->get_member_results() as $member)
        {
            \drupal::logger("wft")->notice("member: ".print_r($member, true));
            $this->markup = $this->get_markup(). $member['first_name']." ".$member['last_name'].PHP_EOL.
                $this->district_text(). " Phone number: ".$this->get_phone().PHP_EOL.
                "Zoom Meeting ID: ".$this->get_location()." Meeting date: ".$this->get_date().PHP_EOL.
                "Meeting Time: ". $this->get_time(). PHP_EOL.
                "NFB Contact: ".$this->get_nfb_contact(). " Phone: ".$this->get_nfb_phone(). PHP_EOL.
                "Attending Meeting: ".$this->get_moc_attendance(). " MOC Contact: ". $this->get_moc_contact().PHP_EOL.
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