<?php
Namespace Drupal\nfb_washington\post_process\report;
use Drupal\nfb_washington\database\base;

class all_member_download extends ind_member_donwlaod
{
    public $full_member_results;
    public function get_full_member_results()
    {return $this->full_member_results;}
    public $full_markup;
    public function geT_full_markup()
{ return $this->full_markup;}
    public $last_state;
    public function get_last_state()
    {
        return $this->last_state;
    }

    public function full_backend()
    {
        ini_set('max_execution_time', 500);
        $this->set_all_members();
        $this->member_loop();
        $this->phpoffice->font_size = '12';
        $this->phpoffice->report_name = "/var/www/html/drupal/web/sites/default/files/all_member_report_washington_seminar.docx";
        $text = $this->geT_full_markup();
        $this->phpoffice->download_doc($text);
    }
    public function set_all_members( )
    {
        $this->database = new base();
        $query = "select * from nfb_washington_members where active = '0' order by state ASC;";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->full_member_results = $this->database->get_result();
        $this->database = null;
    }
    public function member_loop()
    { $year = date('Y');
        $this->full_markup = $year. " Washington Seminar All Member Report".PHP_EOL;
        $this->last_state = null;
        foreach($this->get_full_member_results() as $member)
        {
            $member = get_object_vars($member);
            $this->all_Set_values($member);
            $this->all_set__text_values();
            $this->handle_notes();
            $this->committee_text_maker(); $this->relevant_issue_markup();
            $this->build_text($text);
            $this->full_markup = $this->geT_full_markup(). $text.PHP_EOL."-".PHP_EOL;
            $this->clear_markups();
        }
    }
    public  function  all_Set_values($member)
    {
        $this->form_factory->member_id = $member['member_id'];
        $this->form_factory->civicrm_id = $member['civicrm_contact_id'];
        $this->form_factory->state = $member['state'];
        if($this->get_last_state() != $member['state'])
        {
            $this->last_state = $member['state'];
            $this->full_markup = $this->geT_full_markup()."-ns-".$member['state'].PHP_EOL;
        }
        $this->form_factory->rank = $member['rank'];
        $this->form_factory->district = $member['district'];
        $this->form_factory->get_contact_info();
    }
    public function all_set__text_values()
    {
        if($this->form_factory->get_district() == "Senate")
        {$this->contact_markup = "Senator ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        elseif($this->form_factory->get_state() == "PR")
        {$this->contact_markup = "Resident Commissioner ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        elseif($this->form_factory->get_state() == "DC")
        {$this->contact_markup = "Delegate ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        else {$this->contact_markup = "Representative ".$this->form_factory->get_first_name()." ".$this->form_factory->get_last_name().PHP_EOL;}
        if($this->form_factory->get_district() != "Senate") {
            $this->new_under_name_markup();
        }
        else{
            $this->new_under_name_markup_senate();
        }
    }
    public function clear_markups()
    {
        $this->contact_markup = null;
        $this->issue_markup = null;
        $this->committee_markup = null;
        $this->note_text = null;
    }
    public function new_under_name_markup()
    {
      $this->contact_markup = $this->get_contact_markup()  .PHP_EOL.
      substr($this->form_factory->get__party_name(),0, 1)."-".$this->form_factory->get_state()."-".$this->form_factory->get_district().PHP_EOL;
    }
    public function new_under_name_markup_senate()
    {
        $this->contact_markup = $this->get_contact_markup()  .
            substr($this->form_factory->get__party_name(),0,1)."-".$this->form_factory->get_state()."-".$this-> senate_text().PHP_EOL;
    }
    public function senate_text()
    {
        if( $this->form_factory->get_rank() == "senior")
        {
            return "Sr.";
        }
        else {
            return "Jr.";
        }
    }
}