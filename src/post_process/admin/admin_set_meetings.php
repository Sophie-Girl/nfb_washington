<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\RedirectResponse;
class admin_set_meetings {
    /*################################################################
     *# Connell, Sophia: Creating this script as a solution to the  #
     *# new request for reports on empty meetings. This will gen    #
     *# empty token meetings that will allow for easy reporting      #
      ################################################################ */
    public $database;
    public $year;
    public function get_year()
    {return $this->year;}
    public $member_array;
    public function get_member_array()
    {return $this->member_array;}

    public function set_year()
    {
        $this->year = date('Y');
    }
    public function create_dummy_meetings()
    {
        $this->set_year();
        $this->grab_all_active();
        $this->dummy_meeting_loop();

    }
    public function grab_all_active()
    {
        $query = "SELECT * FROM nfb_washington_members where nfb_washington_members.active != 1;";
        $this->database = new base();
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->member_array = $this->database->get_result();
        $this->database = null;
    }
    public function dummy_meeting_loop()
    {
        $this->database = new base();
        foreach($this->get_member_array() as $moc)
        {
            $moc = get_object_vars($moc);
            $next = $this->check_for_existing($moc);
            if($next === "make new")
            {
                $this->make_new_meeting($moc);
            }
        }
    }
    public function check_for_existing($moc)
    {
        $query = "SELECT * FROM nfb_washington_activities WHERE
member_id = '".$moc['member_id']."' AND meeting_year = '".$this->get_year()."';";
        $key = "activity_id";
        $this->database->select_query($query, $key);
        $existing = false;
        foreach($this->database->get_result() as $meeting)
        {
            if($existing === false )
            {
                $existing = true;
            }
        }
        if($existing === true)
        {
            return "existing";
        }
        else { return  "make new";}
    }
    public function make_new_meeting($moc)
    {
        $feilds = array(
            "member_id" => $moc['member_id'],
            "type" => "meeting",
            "meeting_date" => '1/1/2020',
            "meeting_time" => "12:00: AM",
            "description" => "Washington Seminar Meeting",
            "location" => "TBD",
            "m_o_c_contact" => "TBD",
            "nfb_contact" => "TBD",
            "nfb_phone" => "TBD",
            "moc_attendance" => "1",
            "meeting_year" => $this->get_year(),
            "created_user" => \Drupal::currentUser()->getAccountName(),
            "last_modified_user" => \Drupal::currentUser()->getAccountName(),
        );
        $table = "nfb_washington_activities";
        $this->database->insert_query($table, $feilds);
    }


}