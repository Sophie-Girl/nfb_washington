<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\civicrm\Civicrm;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\civicrm\civicrm_v4;
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
        $form['filter_results'] = array(
          '#type' => 'select',
           '#title' => "Filter Report By",
            '#options' => array(
                'all' => "All Meetings",
                'state' => "Specific State",
                'unscheduled' => "Reps with no meetings"
            ),
            '#required' => true,
        );
        $form['state_select'] = array(
          '#type' => "select",
          "#title" => "Select State",
          '#options' => array(),
          '#states' => $this->state_options()
        );
        $form['file_type'] = array(
            '#type' => 'select',
            '#title' => 'Select File Format',
            '#options' => array(
                'csv' => "Excel CSV File",
                'docx' => "Microsoft Word",
    ),
            '#required' => true,
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );
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
    public function member_query()
    {
        $this->database = new base();
        $query = "Select * from nfb_washington_members where active = 0  and state = '".$this->get_state()."';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->member_results = $this->database->get_result();
        $this->database = null;
    }
    public function handle_meeting_report()
    {
        $member_array = [];
        foreach( $this->get_member_results() as $member)
        {
            $this->set_member_values($member);
            $this->meeting_query($member_array);
        }
        $this->member_results = $member_array;
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
            $member_array[$member_array]['meeting_date'.$this->get_member_id()]['moc_attendance'] = $attendance;
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

    public function district_text()
    {
        if($this->get_district() == "Senate")
        { $district_text = strtoUpper(substr($this->get_rank(), 0,1)).substr($this->get_rank(), 1, 12). " Senator from "
        . $this->get_state();}
        elseif($this->get_state() == "DC")
        {$district_text = "Delegate for ".$this->get_state();}
        elseif($this->get_state() == "PR")
        {$district_text = "Resident Commissioner for ".$this->get_state();}
        else {$district_text = "Representative for ".$this->get_state()." District: ".$this->get_district();}
        return $district_text;
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
    public function process_meeting_query()
    {
        foreach($this->get_member_results() as $meeting)
        {
            $meeting = get_object_vars($meeting);
                $this->member_id = $meeting['member_id'];
                $this->location = $meeting['location'];
                $this->date = $meeting['meeting_date'];
                $this->time = $meeting['meeting_time'];
                $this->nfb_contact = $meeting['nfb_contact'];
                $this->nfb_phone = $meeting['nfb_phone'];
                $this->moc_contact = $meeting['m_o_c_contact'];
                $this->moc_attendance = $meeting['moc_attendance'];
                $this->member_query_meeting_report();


        }
    }
    public function member_query_meeting_report()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_members where member_id = '".$this->get_member_id()."';";
        $key = "member_id";
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $member)
        {
            $member = get_object_vars($member);
            $this->state = $member['state'];
            $this->rank = $member['rank'];
            $this->district = $member['district'];
            $this->civi_id = $member['civicrm_contact_id'];
            $this->civi_query_stuff();
            $this->download_markup();
        }
    }
    public function state_options()
    {
        $civicrm = new Civicrm(); $civicrm->initialize();
        $civicrm_v4 = new civicrm_v4($civicrm);
        $civicrm_v4->civi_entity = "StateProvince";
        $civicrm_v4->civi_mode = "get";
        $civicrm_v4->civi_params = [  'select' => [
        '*',
    ],
  'where' => [
        ['country_id', '=', 1228],
    ],
  'limit' => 60,];
        $result = $civicrm_v4->civi_query_v4();
        \Drupal::logger("civicrm_v4_debug")->notice("result ".print_r($result, true));

        $this->create_the_options($result, $options);
        \Drupal::logger("test_options")->notice("Options ".print_r($options, true));
        return $options;
    }
    public function  create_the_options($result, &$options)
    {
        \Drupal::logger("civicrm_v4_debug")->notice("Result ".print_r($result, true));
        $options = [];
        $count = $result->count(); $current = 0;
        while($count >= $current)
        {
            $state = $result->itemAt($current);
            \Drupal::logger("test")->notice("state ".print_r($state, true));
            $options[$state['abbreviation']] = $state['name'];
            $current++;
        }
    }
}