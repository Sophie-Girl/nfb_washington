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
        $this->download_type($form, $form_state);
        $this->state_select($form, $form_state);
        $this->report_markup($form, $form_state);

    }
    public function state_select(&$form, $form_state)
    {
        $form['state_select'] = array(
          '#type' => 'select',
          '#title' => 'Select State',
          '#options' => $this->state_options(),
          '#required' => false,
            '#ajax' => array(
              'event' => 'change',
              'callback' => "::data_refresh",
              'wrapper' => 'meeting_div'
            ),
        );
    }
    public function report_markup(&$form, $form_state)
    {
        $form['report_markup'] = array(
            '#prefix' => "<div class='meeting_div'> ",
            '#type' => 'item',
            '#markup' => $this->build_markup($form_state),
            '#suffix' => "</div>",
        );
    }
    public function download_type(&$form, $form_state)
    {
        $form['doc_type'] = array(
            '#type' => 'select',
            '#title' => "Downloadable Report Version",
            '#options' => array(
              'state' => "Just Selected State",
              'full' => "Full Report"
            ),
            '#required' => true
        );

    }
    public function state_options()
    {
        $this->set_up_civi($result);
        $this->set_state_options($result, $options);
        return  $options;
    }
    public function set_up_civi(&$result)
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civicrm = new civi_query($civi);
        $this->civicrm->mode = 'get'; $this->civicrm->entity = 'StateProvince';
        $this->civicrm->params = array(
            'sequential' => 1,
            'country_id' => "1228",
            'options' => ['limit' => 60],
        );
        $this->civicrm->civi_query($result);
    }
    public function set_state_options($result, &$options)
    {
        foreach($result['values'] as $state)
        {
            if($state['id'] != "1052" && $state['id'] != "1053" &&$state['id'] != "1055"
                && $state['id'] != "1057" && $state['id'] != "1058" && $state['id'] != "1059"
                && $state['id'] != "1060" && $state['id'] != "1061"){
                $options[$state['abbreviation']] = $state['name'];}
        }
        ksort($options);
    }
    Public Function build_markup(FormStateInterface $form_state)
    {
        if($form_state->getValue("state_select") == "")
        {$markup = "<p> Select a state to view a preview of the report</p>";}
        else{
            $this->start_webpage_markup();
            $markup = $this->get_markup();
        }
        return $markup;
    }
    public function set_state(FormStateInterface $form_state)
    {
        $this->state = $form_state->getValue("state-select");
    }
    public function member_query()
    {
        $this->database = new base();
        $query = "Select * from nfb_washington_members where state = '".$this->get_state()."'
        and active = 0 ;";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->member_results = $this->database->select_query($query, $key);
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
        "<table>
        <tr><th class='table-header'>Member of Congress Name:</th><th class='table-header'> Member of Congress Name phone:</th><th class='table-header'>District/Senate Rank</th><th class ='table-header'>
        Zoom Meeting ID:</th><th class='table-header'> Meeting Time</th> <th class='table-header'>NFB Contact Information
        </th><th class='table-header'>MOC Attendance</th><th class='table-header'> MOC Contact</th></tr>";
        foreach($this->get_member_results() as $member)
        {
            $this->set_member_values($member);
            $this->markup = $this->get_markup(). "<tr><td>".$this->get_first_name()." ".$this->get_last_name()."</td>
<td>".$this->get_phone()."</td><td>".$this->district_text()."</td><td>".$this->get_location()."</td>".$this->get_date()." ".$this->get_time()."<
<td>".$this->get_nfb_contact()." Phone: ".$this->get_phone()."</td><td>".$this->get_moc_contact()."</td><td>".$this->get_moc_attendance()."</td></tr>";
        }

    }
    public function downlaod_markup()
    {
        $this->member_query();
        $this->markup = $this->get_markup()."-----------------------------------------------------------------------".PHP_EOL;
        foreach($this->get_member_results() as $member)
        {
            $this->set_member_values($member);
            $this->markup = $this->get_markup(). $this->get_first_name()." ".$this->get_last_name().PHP_EOL.
                $this->district_text(). " Phone number: ".$this->get_phone().PHP_EOL.
                "Zoom Meeting ID: ".$this->get_location()." Meeting date: ".$this->get_date().PHP_EOL.
                "Meeting Time: ". $this->get_time(). PHP_EOL.
                "NFB Contact: ".$this->get_nfb_contact(). " Phone: ".$this->get_nfb_phone(). PHP_EOL.
                "Attending Meeting: ".$this->get_moc_attendance(). " MOC Contact: ". $this->get_moc_contact().PHP_EOL.
                "-----------------------------------------------------------------------".PHP_EOL;
        }
    }
    public function district_text()
    {
        if($this->get_district() == "Senate")
        { $district_text = strtoUpper(substr($this->get_rank(), 0,1)).substr($this->get_rank(), 1, 12). " Senator from"
        . $this->get_state();}
        elseif($this->get_state() == "DC")
        {$district_text = "Delegate for ".$this->get_state();}
        elseif($this->get_state() == "PR")
        {$district_text = "Resident Commissioner for ".$this->get_state();}
        else {$district_text = "Representative for ".$this->get_state()." District: ".$this->get_district();}
        return $district_text;
    }

}