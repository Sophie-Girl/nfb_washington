<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\civicrm\Civicrm;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\database\base;

class individual_member_report
{
    public $database;
    public $civi_query;
    public $user_permission;
    public function get_user_permission()
    {return $this->user_permission;}
    public $member_id;
    public function  get_member_id()
    {return $this->member_id;}
    public $sql_result;
    public function get_sql_result()
    {return $this->sql_result;}
    public $civicrm_id;
    public function get_civicrm_id()
    {return $this->civicrm_id;}
    public $district;
    public function get_district()
    {return $this->district;}
    public $rank;
    public function get_rank()
    {return $this->rank;}
    public $state;
    public function get_state()
    {return $this->state;}
    public $propublica_id;
    public function get_propublica_id()
    {return $this->propublica_id;}
    public $first_name;
    public function get_first_name()
    {return $this->first_name;}
    public $last_name;
    public function get_last_name()
    {return $this->last_name;}
    public $phone_number;
    public function get_phone_number()
    {return $this->phone_number;}
    public $contact_markup;
    public function get_contact_markup()
    {return $this->contact_markup;}
    public $link_id;
    public function get_link_id()
    {return $this->link_id;}
    public $note_markup;
    public function get_note_markup()
    {return $this->note_markup;}
    public $issue1;
    public function get_issue_1()
    {return $this->issue1;}
    public  $issue2;
    public function get_issue_2()
    {return $this->issue2;}
    public  $issue3;
    public function get_issue_3()
    {return $this->issue3;}
    public  $issue4;
    public function get_issue_4()
    {return $this->issue4;}
    public  $issue5;
    public function get_issue_5()
    {return $this->issue5;}
    public $issue_name_1;
    public function  get_issue_name_1()
    {return $this->issue_name_1;}
    public $issue_name_2;
    public function  get_issue_name_2()
    {return $this->issue_name_2;}
    public $issue_name_3;
    public function  get_issue_name_3()
    {return $this->issue_name_3;}
    public $issue_name_4;
    public function  get_issue_name_4()
    {return $this->issue_name_4;}
    public $issue_name_5;
    public function  get_issue_name_5()
    {return $this->issue_name_5;}
    public $issue_count;
    public function get_issue_count()
    {return $this->issue_count;}
    public $committee_name;
    public function get_committee_name()
    {return $this->committee_name;}
    public $party_name;
    public function get__party_name()
    {return $this->party_name;}
    public $committee_markup;
    public $committee_member_match;
    public function  get_committee_member_match()
    {
        return $this->committee_member_match;
    }
    public function get_committee_markup()
    {return $this->committee_markup;}
    public $issue_markup;
    public function get_issue_markup()
    {return $this->issue_markup;}
    public function set_user_permission()
    {
        $user = \Drupal::currentUser(); $permission = "false";
        foreach($user->getRoles() as $role)
        {
            if($role == "Administrator")
            {$permission = "true";}
            elseif($role == "nfb_washington_admin")
            {$permission = "true";}
        }
        $this->user_permission = $permission;
    }
    public function build_report_page(&$form, $form_state, $member)
    {
        $form['report_markup'] = array(
          '#type' => "item",
          "#markup" => $this->build_markup($member)
        );
        $form['member_value'] = array(
            '#type' => 'textfield',
            '#value' => $member,
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Member Id"
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );
    }
    public function build_markup($member)
    {
        $this->member_id = $member;
        $this->set_issue_count();
        $this->set_user_permission();
        $this->build_contact_markup();
        $this->set_note_markup();
        $this->relevant_committees_markup();

        $this->relevant_issue_markup();
        $markup = $this->get_contact_markup().$this->get_note_markup().
            $this->get_committee_markup(). $this->get_issue_markup();
        return $markup;

    }
    public function member_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_members where member_id = '".$this->get_member_id()."';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
        $this->database = null;
        $this->set_member_values();
    }
    public function set_member_values()
    {
        foreach ($this->get_sql_result() as $member)
        {
            $member = get_object_vars($member);
            $this->civicrm_id = $member['civicrm_contact_id'];
            $this->state = $member['state'];
            $this->rank = $member['rank'];
            $this->district = $member['district'];
            $this->propublica_id = $member['propublica_id'];
        }
    }
    public function get_contact_info()
    {
    /*    $civi = new Civicrm(); $civi->initialize();
        $this->civi_query = new civi_query($civi);
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = 'get';
        $this->civi_query->civi_params = array(
            'sequential' => 1,
            'id' => $this->get_civicrm_id(),
        );
        $this->civi_query->civi_query();
        foreach($this->civi_query->get_civicrm_result()['values'] as $contact)
        {
            $this->first_name = $contact['first_name'];
            $this->last_name =  $contact['last_name'];
            $this->phone_number = $contact['phone'];
        }
        $this->civi_query = null;

    v4 instead  */
        $this->contact_info_v4();
    }
    public function contact_info_v4()
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civi_query = new civicrm_v4($civi);
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = 'get';
        $this->civi_query->civi_params =  [
            'select' => [
                '*',
                'phone.*',
            ],
            'join' => [
                ['Phone AS phone', 'LEFT', ['phone.contact_id', '=', 'id']],
            ],
            'where' => [
                ['id', '=', $this->get_civicrm_id()],
                ['phone.is_primary', '=', TRUE],
            ],
            'checkPermissions' => FALSE,
            'limit' => 25,
        ];
        $result = $this->civi_query->civi_query_v4();
        $res = $result->first();
        $this->first_name = $res['first_name'];
        $this->last_name =  $res['last_name'];
        $this->phone_number = $res['phone.phone'];
        $this->civi_query = null;
        $this->party_info();
    }
    public function party_info()
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civi_query = new civicrm_v4($civi);
        $this->civi_query->civi_entity = "Relationship";
        $this->civi_query->civi_mode = "get";
        $this->civi_query->civi_params = [
            'select' => [
                '*',
            ],
            'where' => [
                ['contact_id_b', '=', $this->get_civicrm_id()],
                ['relationship_type_id', '=', 55],
            ],
            'limit' => 25,
        ];
        $result = $this->civi_query->civi_query_v4();
        $relat = $result->first();
        $party_id = $relat['contact_id_a'];
        $this->party_name = $this->get_party_dispaly_name($party_id);
        $this->civi_query = null;

    }
    public function get_party_dispaly_name($party_id)
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civi_query = new civicrm_v4($civi);
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = 'get';
        $this->civi_query->civi_params =  [
            'select' => [
                '*',
            ],
            'where' => [
                ['id', '=', $party_id],
            ],
            'limit' => 25,
        ];
        $result = $this->civi_query->civi_query_v4();
        $party = $result->first();
        return $party['display_name'];
    }
    public function build_contact_markup()
    {
        $this->member_query();
        $this->get_contact_info();
        if($this->get_district() == "Senate")
        {$this->contact_markup = "<h2>Senator ".$this->get_first_name()." ".$this->get_last_name()."</h2>";}
        elseif($this->get_state() == "PR")
        {$this->contact_markup = "<h2>Resident Commissioner ".$this->get_first_name()." ".$this->get_last_name()."</h2>";}
        elseif($this->get_state() == "DC")
        {$this->contact_markup = "<h2>Delegate ".$this->get_first_name()." ".$this->get_last_name()."</h2>";}
        else {$this->contact_markup = "<h2>Representative ".$this->get_first_name()." ".$this->get_last_name()."</h2>";}
        if($this->get_district() == "Senate")
        {
            $this->senate_contact_markup();
        }
        else{
             $this->house_contact_markup();
        }
    }
    public function senate_contact_markup()
    {
        $this->contact_markup = $this->get_contact_markup()."
        <p class='right-side'>State: ".$this->get_state()." <span>Rank: ".strtoupper(substr($this->get_rank(),0, 1)).substr($this->get_rank(), 1,20)."</span></p>
        <p class='right-side'>Phone: ".$this->get_phone_number()."</p>
        <p>Party: ".$this->get__party_name()."</p>";
    }
    public function house_contact_markup()
    {
        $this->contact_markup = $this->get_contact_markup()."
        <p class='right-side'>State: ".$this->get_state()." <span>District: ".strtoupper(substr($this->get_district(),0, 1)).substr($this->get_district(), 1,20)."</span></p>
        <p>Phone: ".$this->get_phone_number()."</p>
        <p>Party: ".$this->get__party_name()."</p>";
    }
    public function set_note_markup()
    {
        $this->get_notes();
        $this->note_link_loop();
    }
    public function get_notes()
    {
        $this->database = new base();
        $query = "select   * from nfb_washington_note_link where entity_id = '".$this->get_member_id()."'
        and table_name = 'nfb_washington_members';";
        $key = "link_id";
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
        $this->database = null;
    }
    public function note_link_loop()
    {
        $note_markup = "<h3>Notes</h3>";
        $links =  $this->get_sql_result();
        $count = 0;
        foreach($links as $link)
        {
            $link = get_object_vars($link);
            $this->link_id = $link['link_id'];
            $note_id = $link['note_id'];
            $this->note_loop($note_markup, $note_id, $count);
        }
        if($count == 0)
        {
            $note_markup = "<h3>Notes</h3>
        <p>No notes exist for this member</p>";
            if($this->get_user_permission() == "true")
            {
                $note_markup = $note_markup."<p><a href='/nfb_washington/admin/note/create' class='button-1' role ='button' aria-label='create new note'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Create New&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='/nfb_washington/admin/note_link' class='button-2' role='button' aria-label='Link Existing Note'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Link Existing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>";
            }
        }$this->note_markup = $note_markup;

    }
    public function note_loop(&$note_markup, $note_id, &$count)
    {

        $year = date('Y');
        $this->database = new base();
        $query = "select * from nfb_washington_note where note_id = '".$note_id."';";
        $key = "note_id";
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $note)
        { $note = get_object_vars($note);
        if($note['note_year'] == $year)
        {
         $note_markup = $note_markup. "<p>- ".$note['note']."</p>";
         if($this->get_user_permission() == "true")
         {
             $note_markup = $note_markup."<p><a href='/nfb_washington/admin/note/create' class='button-1' role ='button' aria-label='create new note'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Create New&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='/nfb_washington/admin/note_link' class='button-2' role='button' aria-label='Link Existing Note'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Link Existing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href='/nfb_washington/admin/notes/link/remove/".$this->get_link_id()."' class='button-3' role='button' aria-label='Remove note'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Remove This Note&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>";
         }
         $count++;
        }}
        $this->database = null;
    }
    public function relevant_committees_markup()
    {
        $this->committee_markup = "<h3>Committee Info</h3>";
        $this->set_issues();
        $this->find_committee_1();
        if($this->get_issue_count() > 1)
        {
        $this->find_committee_2();}
        if($this->get_issue_count() > 2)
        {$this->find_committee_3();}
        if($this->get_issue_count() > 3)
        {$this->find_committee_4();}
        if($this->get_issue_count() > 4)
        {$this->find_committee_5();}
        if($this->get_committee_member_match() != "true")
        { $this->committee_markup = $this->get_committee_markup(). "<p>They do not serve on any relevant committees</p>";}

    }
    public function set_issues()
    {

        $this->database = new base();$year = date("Y");
        $query = "select * from  nfb_washington_issues where issue_year = '".$year."'
         order by issue_id ASC";
        $key = 'issue_id';
        $this->database->select_query($query, $key);
        $count = 1;

        foreach($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            switch ($count)
            {
                case 1:

                    $this->issue1 = $issue['issue_id'];
                    $this->issue_name_1 = $issue['issue_name'];break;
                case 2:
                    $this->issue2 = $issue['issue_id'];
                    $this->issue_name_2 = $issue['issue_name'];break;
                case 3:
                    $this->issue3 = $issue['issue_id'];
                    $this->issue_name_3 = $issue['issue_name']; break;
                case 4:
                    $this->issue4 = $issue['issue_id'];
                    $this->issue_name_4 = $issue['issue_name']; break;
                case 5:
                    $this->issue5 = $issue['issue_id'];
                    $this->issue_name_5 = $issue['issue_name']; break;

            }
            $count++;
        }
        $this->database = null;
    }
    public function find_committee_1()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '" . $this->get_issue_1() . "';";
        $key = 'link_id';
        $this->database->select_query($query, $key);
        $committee_id_array = null;
        $count = 1;
        foreach ($this->database->get_result() as $committee) {
            $committee = get_object_vars($committee);
            $committee_id_array[$count] = $committee['link_id'];
            $count++;
        }
        if($committee_id_array != null){
            $count = 1;
            $this->committee_loop($committee_id_array, $count);}
    }
        public function find_committee_2()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '".$this->get_issue_2()."';";
        $key = 'link_id';
        $this->database->select_query($query, $key);
        $committee_id_array = null;  $count =1;
        foreach($this->database->get_result() as $committee)
        {
            $committee = get_object_vars($committee);
            $committee_id_array[$count] = $committee['committee_id'];
            $count++;
        }
            $count =2;
        if($committee_id_array != null){
            $this->committee_loop($committee_id_array, $count);}

    }
    public function find_committee_3()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '".$this->get_issue_3()."';";
        $key = 'link_id';
        $this->database->select_query($query, $key);
        $committee_id_array = null;  $count =1;
        foreach($this->database->get_result() as $committee)
        {
            $committee = get_object_vars($committee);
            $committee_id_array[$count] = $committee['link_id'];
            $count++;
        }
            $count =3;
        if($committee_id_array != null){
            $this->committee_loop($committee_id_array, $count);}

    }
    public function find_committee_4()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '".$this->get_issue_4()."';";
        $key = 'link_id';
        $this->database->select_query($query, $key);
        $committee_id_array = null;  $count =1;
        foreach($this->database->get_result() as $committee)
        {
            $committee = get_object_vars($committee);
            $committee_id_array[$count] = $committee['link_id'];
            $count++;
        }
        $count =4;
        if($committee_id_array != null){
            $this->committee_loop($committee_id_array, $count);}
    }
    public function find_committee_5()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '".$this->get_issue_5()."';";
        $key = 'link_id';
        $this->database->select_query($query, $key);
        $committee_id_array = null;  $count =1;
        foreach($this->database->get_result() as $committee)
        {
            $committee = get_object_vars($committee);
            $committee_id_array[$count] = $committee['link_id'];
            $count++;
        }
        $count =5;
        if($committee_id_array != null){
            $this->committee_loop($committee_id_array, $count);}
    }
    public function committee_loop($committee_id_array, $count )
    {
        $match = "false";
        foreach($committee_id_array as $committee)
        {
            $this->set_committee_name($committee);
            $this->member_link_search($match, $count, $committee);
        }
        if($this->get_committee_member_match() != "true")
        {$this->committee_member_match = $match;}
    }
    public function set_committee_name($committee)
    {
        if ($committee != "" || $committee != null) {
        $this->database = new base();
        $query = "select *  from nfb_washington_committee where 
    committee_id = '" . $committee . "';";
        $key = 'committee_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $record) {
            $record = get_object_vars($record);
            $this->committee_name = $record['committee_name'];
        }
    }
    }
    public function member_link_search(&$match, $count, $committee)
    {
        if ($committee != "" || $committee != null) {
        $this->database = new base();
        $query = "select *  from nfb_washington_committee_mem where 
    committee_id = '".$committee."';";
        $key = "com_mem_id";
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $link) {
            $link = get_object_vars($link);
            if ($match == "false") {
                if ($this->get_member_id() == $link['member_id']) {
                    $match = "true";
                    if ($count == 1) {
                        $this->committee_markup = $this->get_committee_markup() . "
<p> Serves on the " . $this->get_committee_name() . " which the " . $this->get_issue_name_1() . " will pass through</p>";
                    } elseif ($count == 2) {
                        $this->committee_markup = $this->get_committee_markup() . "
<p> Serves on the " . $this->get_committee_name() . " which the " . $this->get_issue_name_2() . " will pass through</p>";
                    } elseif ($count == 3) {
                        $this->committee_markup = $this->get_committee_markup() . "
<p> Serves on the " . $this->get_committee_name() . " which the " . $this->get_issue_name_3() . " will pass through</p>";
                    }
                    elseif ($count == 4) {
                        $this->committee_markup = $this->get_committee_markup() . "
<p> Serves on the " . $this->get_committee_name() . " which the " . $this->get_issue_name_4() . " will pass through</p>";
                    }
                    elseif ($count == 5) {
                        $this->committee_markup = $this->get_committee_markup() . "
<p> Serves on the " . $this->get_committee_name() . " which the " . $this->get_issue_name_5() . " will pass through</p>";
                    }

                }
            }
        }

        }
    }
    public function set_issue_count()
    {
        $issue_count = null;
        $query = "select * from nfb_washington_config where setting = 'issue_count' and active = '0';";
        $key = 'config_id';
        $this->database = new base();
        $this->database->select_query($query, $key);
        if($this->database->get_result() != "error"|| $this->database->get_result() != array())
        {
            foreach($this->database->get_result() as $setting)
            {
                if($issue_count == null){
                    $setting = get_object_vars($setting);
                    $issue_count = $setting['value'];}
            }
        }
        $this->issue_count = $issue_count;
        $this->database = null;
    }
    public function relevant_issue_markup()
    {
        $this->set_issues();
        $this->find_primary_issue_1();
        if($this->get_issue_count() > 1)
        {$this->find_primary_issue_2();}
        if($this->get_issue_count() > 2)
        {$this->find_primary_issue_3();}
        if($this->get_issue_count() >3)
        {$this->find_primary_issue_4();}
        if($this->get_issue_count() > 4)
        {$this->find_primary_issue_5();}
    }
    public function find_primary_issue_1()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->get_issue_1()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = "<h3>Past Ratings on our Issues</h3>
<p>".$this->get_issue_name_1()."</p>
    <p>No past info on ".$this->get_issue_name_1()."</p>";}
            else{
                $this->issue_markup = "<h3>Past Ratings on our Issues</h3>
<p>".$this->get_issue_name_1()."</p>";
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->get_issue_1();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }

        }
        $this->database = null;
    }
    public function find_primary_issue_2()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->get_issue_2()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup(). "
                <p>".$this->get_issue_name_2()."</p>
    <p>No past info on ".$this->get_issue_name_2()."</p>";}
            else{
                $this->issue_markup = $this->get_issue_markup()."
                <p>".$this->get_issue_name_2()."</p>";
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->get_issue_2();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function find_primary_issue_3()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->get_issue_3()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup(). "
                <p>".$this->get_issue_name_3()."</p>
    <p>No past info on ".$this->get_issue_name_3()."</p>";}
            else{
                $this->issue_markup = $this->get_issue_markup()."
                <p>".$this->get_issue_name_3()."</p>";
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->get_issue_3();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function find_primary_issue_4()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->get_issue_4()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup(). "
                <p>".$this->get_issue_name_4()."</p>
    <p>No past info on ".$this->get_issue_name_4()."</p>";}
            else{
                $this->issue_markup = $this->get_issue_markup()."
                <p>".$this->get_issue_name_4()."</p>";
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->get_issue_4();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function find_primary_issue_5()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->get_issue_5()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup(). "
                <p>".$this->get_issue_name_5()."</p>
    <p>No past info on ".$this->get_issue_name_5()."</p>";}
            else{
                $this->issue_markup = $this->get_issue_markup()."
                <p>".$this->get_issue_name_5()."</p>";
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->get_issue_5();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function  find_all_all_repeat_uses($primary_id, $issue_id)
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where primary_issue_id = '".$primary_id."' 
        and issue_id != '".$issue_id."';";
        $key = 'issue_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            $this->issue_rating_switch($issue);
            $this->issue_markup = $this->get_issue_markup().
                "<p>".$issue['issue_year'].": Rating".$issue['rating']."</p>
    <p>Comment: ".$issue['comment']."</p>";
        }
        $this->find_original($primary_id);
        $this->database = null;

    }
    public function issue_rating_switch(&$issue)
    {
        switch($issue['rating'])
        {
            case 'u':
                $issue['rating'] = "Undecided"; break;
            case 'y':
                $issue['rating'] = "Yes"; break;
            case 'n':
                $issue['rating'] = "No"; break;
            case "nd":
                $issue['rating'] = "Not Discussed"; break;
        }
    }
    public function find_original($primary_id)
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id ='".$primary_id."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            $this->issue_rating_switch($issue);
            $this->issue_markup = $this->get_issue_markup().
                "<p>".$issue['issue_year'].": Rating".$issue['rating']."</p>
    <p>Comment: ".$issue['comment']."</p>";
        }
        $this->database = null;
    }





}
