<?php
Namespace Drupal\nfb_washington\post_process\report;
use Drupal\core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\form_factory\report\individual_member_report;
use Drupal\nfb_washington\microsoft_office\html_to_word;

class  ind_member_donwlaod
{
    public $phpoffice;
    public $form_factory;
    public function __construct(html_to_word $html_to_word, individual_member_report $individual_member_report)
    {
        $this->phpoffice = $html_to_word;
        $this->form_factory = $individual_member_report;
    }
    public $database;
    public $civi_query;
    public $note_text;
    public function get_note_text()
    {return $this->note_text;}
    public $issue_markup;
    public function get_issue_markup()
    {return $this->issue_markup;}
    public $committee_markup;
    public function get_committee_markup()
    {return $this->committee_markup;}
    public $contact_markup;
    public function get_contact_markup()
    {return $this->contact_markup;}
    public $issue_count;
    public function get_issue_count()
    {return $this->issue_count;}

    public function backend(FormStateInterface $form_state)
    {
        $this->set_php_office_values(); $this->member_id_set($form_state);
        $this->set_text_values(); $this->handle_notes();
        $this->committee_text_maker(); $this->relevant_issue_markup();
        $this->build_text($text);
        $this->phpoffice->download_doc($text);
    }
    public function build_text(&$text)
    {
        \Drupal::logger("xml_check")->notice($this->get_note_text());
        $text = $this->get_contact_markup().$this->get_note_text().$this->get_committee_markup().$this->get_issue_markup().PHP_EOL;

    }
    public function set_php_office_values()
    {
        $this->phpoffice->font_size = 18;
        $this->phpoffice->report_name = "individual_member_of_congress_report.docx";
    }
    public function member_id_set(FormStateInterface $form_state){
        $this->form_factory->member_id = $form_state->getValue("member_value");
    }
    public function set_text_values()
    {
        $this->form_factory->member_query();
        $this->form_factory->set_member_values();
        $this->form_factory->get_contact_info();
        if($this->form_factory->get_district() == "Senate")
        {$this->contact_markup = "Senator ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        elseif($this->form_factory->get_state() == "PR")
        {$this->contact_markup = "Resident Commissioner ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        elseif($this->form_factory->get_state() == "DC")
        {$this->contact_markup = "Delegate ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        else {$this->contact_markup = "Representative ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        if($this->form_factory->get_district() == "Senate")
        {
            $this->senate_contact_markup();
        }
        else{
            $this->house_contact_markup();
        }

    }
    public function senate_contact_markup()
    {
        $this->contact_markup = $this->get_contact_markup().
        "State: ".$this->form_factory->get_state()."      Rank: ".strtoupper(substr($this->form_factory->get_rank(),0, 1)).substr($this->form_factory->get_rank(), 1,20).PHP_EOL
        ."Phone: ".$this->form_factory->get_phone_number().PHP_EOL;
    }
    public function house_contact_markup()
    {
        $this->contact_markup = $this->get_contact_markup().PHP_EOL.
        "State: ".$this->form_factory->get_state()." District: ".strtoupper(substr($this->form_factory->get_district(),0, 1)).substr($this->form_factory->get_district(), 1,20).PHP_EOL.
       "Phone: ".$this->form_factory->get_phone_number().PHP_EOL;
    }
    public function handle_notes()
    {
        $this->form_factory->get_notes(); $count  = 0;
        $this->note_text = "Notes: ".PHP_EOL;
        foreach ($this->form_factory->get_sql_result() as $note_link)
        {
            $note_link= get_object_vars($note_link);
            $year = date('Y');
            $note_id = $note_link['note_id'];
            $this->database = new base();
            $query = "select * from nfb_washington_note where note_id = '".$note_id."';";
            $key = "note_id";
            $this->database->select_query($query, $key);
            foreach($this->database->get_result() as  $note) {
                $note = get_object_vars($note);
                $this->note_type_switch($note, $note_type);
                $this->note_text = $this->get_note_text() . "-   " . $note['note'] . PHP_EOL;
            }
        }
    }
    public function note_type_switch($note, &$note_type)
    {
        switch($note['note_type'])
        {
            case "bill_sponsor":
                $note_type = "Bill Sponsor: "; break;
            case "bill_co_sponsor":
                $note_type = "Bill Co-Spnosor: ";break;
            case "state_convention":
                $note_type = "Spoke at a State Convention: "; break;
            case "national_convention":
                $note_type = "Spoke at National Convention: ";break;
        }
    }
    public function committee_text_maker()
    {
        $this->form_factory->set_issues();
        $this->committee_markup = "Committees: ".PHP_EOL;
        $this->form_factory->set_issue_count();
        $this->find_committee_1();
        if($this->form_factory->get_issue_count() > 1)
        {$this->find_committee_2();}
        if($this->form_factory->get_issue_count() > 2){
        $this->find_committee_3();}
        if($this->form_factory->get_issue_count() > 3){
            $this->find_committee_4();}
        if($this->form_factory->get_issue_count() > 4){
            $this->find_committee_5();}
        if($this->form_factory->get_committee_member_match() != "true")
        {$this->committee_markup = $this->get_committee_markup()." They do not serve on any relevant committees";}

    }
    public function find_committee_1()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '" . $this->form_factory->get_issue_1() . "';";
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
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '" . $this->form_factory->get_issue_2() . "';";
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
            $count = 2;
            $this->committee_loop($committee_id_array, $count);}
    }
    public function find_committee_3()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '" . $this->form_factory->get_issue_3() . "';";
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
            $count = 3;
            $this->committee_loop($committee_id_array, $count);}
    }
    public function find_committee_4()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '" . $this->form_factory->get_issue_4() . "';";
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
            $count = 4;
            $this->committee_loop($committee_id_array, $count);}
    }
    public function find_committee_5()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where issue_id = '" . $this->form_factory->get_issue_5() . "';";
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
            $count = 5;
            $this->committee_loop($committee_id_array, $count);}
    }
    public function committee_loop($committee_id_array, $count)
    {
        $match = "false";
        foreach($committee_id_array as $committee)
        {
            $this->form_factory->set_committee_name($committee);
            $this->match_maker($match, $count, $committee);
        }
        if($this->form_factory->get_committee_member_match() != "true")
        {$this->form_factory->committee_member_match = $match;}
    }
    public function  match_maker(&$match, $count, $committee)
    {
        if ($committee != "" || $committee != null) {
            $this->database = new base();
            $query = "select *  from nfb_washington_committee_mem where 
    committee_id = '" . $committee . "';";
            $key = "com_mem_id";
            $this->database->select_query($query, $key);
            foreach ($this->database->get_result() as $link) {
                $link = get_object_vars($link);
                if ($match == "false") {
                    if ($this->form_factory->get_member_id() == $link['member_id']) {
                        $match = "true";
                        if ($count == 1) {
                            $this->committee_markup = $this->get_committee_markup() . "
 Serves on the " . $this->form_factory->get_committee_name() . " which the " . $this->form_factory->get_issue_name_1() . " will pass through" . PHP_EOL;
                        } elseif ($count == 2) {
                            $this->committee_markup = $this->get_committee_markup() . "
Serves on the " . $this->form_factory->get_committee_name() . " which the " . $this->form_factory->get_issue_name_2() . " will pass through" . PHP_EOL;
                        } elseif ($count == 3) {
                            $this->committee_markup = $this->get_committee_markup() . "
 Serves on the " . $this->form_factory->get_committee_name() . " which the " . $this->form_factory->get_issue_name_3() . " will pass through" . PHP_EOL;
                        }
                        elseif ($count == 4) {
                            $this->committee_markup = $this->get_committee_markup() . "
 Serves on the " . $this->form_factory->get_committee_name() . " which the " . $this->form_factory->get_issue_name_4() . " will pass through" . PHP_EOL;
                        }
                        elseif ($count == 5) {
                            $this->committee_markup = $this->get_committee_markup() . "
 Serves on the " . $this->form_factory->get_committee_name() . " which the " . $this->form_factory->get_issue_name_5() . " will pass through" . PHP_EOL;
                        }
                    }
                }
            }

        }
    }
    public function relevant_issue_markup()
    {
        $this->form_factory->set_issues();
        $this->form_factory->set_issue_count();
        $this->find_primary_issue_1();
        if($this->form_factory->get_issue_count() > 1)
        {$this->find_primary_issue_2();}
        if($this->form_factory->get_issue_count() > 2)
        {$this->find_primary_issue_3();}
        if($this->form_factory->get_issue_count() > 3)
        {$this->find_primary_issue_4();}
        if($this->form_factory->get_issue_count() > 4)
        {$this->find_primary_issue_5();}
    }
    public function find_primary_issue_1()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->form_factory->get_issue_1()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = "Past Ratings on our Issues".PHP_EOL
.$this->form_factory->get_issue_name_1().PHP_EOL.
    "No past info on ".$this->form_factory->get_issue_name_1().PHP_EOL;}
            else{
                $this->issue_markup = "Past Ratings on our Issues".PHP_EOL.
$this->form_factory->get_issue_name_1().PHP_EOL;
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->form_factory->get_issue_1();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }

        }
        $this->database = null;
    }
    public function find_primary_issue_2()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->form_factory->get_issue_2()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup().PHP_EOL.
            $this->form_factory->get_issue_name_2().PHP_EOL.
    "No past info on ".$this->form_factory->get_issue_name_2().PHP_EOL;}
            else{
                $this->issue_markup = $this->get_issue_markup()
                .$this->form_factory->get_issue_name_2().PHP_EOL;
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->form_factory->get_issue_2();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function find_primary_issue_3()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->form_factory->get_issue_3()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup().
                $this->form_factory->get_issue_name_3().PHP_EOL.
    "No past info on ".$this->form_factory->get_issue_name_3().PHP_EOL;}
            else{
                $this->issue_markup = $this->get_issue_markup().
                $this->form_factory->get_issue_name_3().PHP_EOL;
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->form_factory->get_issue_3();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function find_primary_issue_4()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->form_factory->get_issue_4()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup().
                $this->form_factory->get_issue_name_4().PHP_EOL.
                "No past info on ".$this->form_factory->get_issue_name_4().PHP_EOL;}
            else{
                $this->issue_markup = $this->get_issue_markup().
                    $this->form_factory->get_issue_name_4().PHP_EOL;
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->form_factory->get_issue_4();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function find_primary_issue_5()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->form_factory->get_issue_5()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $primary_id = null;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$this->issue_markup = $this->get_issue_markup().
                $this->form_factory->get_issue_name_5().PHP_EOL.
                "No past info on ".$this->form_factory->get_issue_name_5().PHP_EOL;}
            else{
                $this->issue_markup = $this->get_issue_markup().
                    $this->form_factory->get_issue_name_5().PHP_EOL;
                $primary_id =  $issue['primary_issue_id'];
                $issue_id = $this->form_factory->get_issue_5();
                $this->find_all_all_repeat_uses($primary_id, $issue_id);
            }
        }
        $this->database = null;
    }
    public function  find_all_all_repeat_uses($primary_id, $issue_id)
    {
        $this->database = new base();
        $query = "select * from nfrb_washington_issues where primary_issue_id = '".$primary_id."' 
        and issue_id != '".$issue_id."';";
        $key = 'issue_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            $this->issue_rating_switch($issue);
            $this->issue_markup = $this->get_issue_markup().
                $issue['issue_year'].": Rating".$issue['rating']."Comment: ".$issue['comment'].PHP_EOL;
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
                $issue['issue_year'].": Rating".$issue['rating'].PHP_EOL.
    "Comment: ".$issue['comment'].PHP_EOL;
        }
        $this->database = null;
    }






}