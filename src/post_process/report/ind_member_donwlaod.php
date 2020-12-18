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
    public function backend(FormStateInterface $form_state)
    {
        $this->set_php_office_values(); $this->member_id_set($form_state);

    }
    public function set_php_office_values()
    {
        $this->phpoffice->font_size = 18;
        $this->phpoffice->report_name = "individual_member_of_congress_report";
    }
    public function member_id_set(FormStateInterface $form_state){
        $this->form_factory->member_id = $form_state->getValue("member_value");
    }
    public function set_text_values()
    {
        $this->form_factory->member_query();
        $this->form_factory->set_member_values();
        $this->form_factory->get_contact_info();

    }
    public function handle_notes()
    {
        $this->form_factory->get_notes(); $count  = 0;
        $this->note_text = "NMotes: ".PHP_EOL;
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
                $this->note_text = $this->get_note_text() . "-   " . $note_type . $note['note'] . PHP_EOL;
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
    public function committee_loop($committee_id_array, $count)
    {
        $match = "false";
        foreach($committee_id_array as $committee)
        {
            $this->form_factory->set_committee_name($committee);

        }
        if($this->form_factory->get_committee_member_match() != "true")
        {$this->form_factory->committee_member_match = $match;}
    }
    public function  match_maker()
    {

    }






}