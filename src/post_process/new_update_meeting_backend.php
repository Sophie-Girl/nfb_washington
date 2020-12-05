<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\RedirectResponse;

class  new_update_meeting_backend
{
    public $database;
    public $meeting_id;
    public $member_id;
    public $meeting_location;
    public $meeting_time;
    public $meeting_date;
    public $moc_contact;
    public $nfb_contact;
    public $nfb_phone;
    public $comments;
    public $moc_attendance;

    public function get_meeting_id()
    {
        return $this->meeting_id;
    }

    public function get_member_id()
    {
        return $this->member_id;
    }

    public function get_meeting_location()
    {
        return $this->meeting_location;
    }

    public function get_meeting_time()
    {
        return $this->meeting_time;
    }

    public function get_meeting_date()
    {
        return $this->meeting_date;
    }

    public function get_moc_contact()
    {
        return $this->moc_contact;
    }

    public function get_nfb_contact()
    {
        return $this->nfb_contact;
    }

    public function get_nfb_phone()
    {
        return $this->nfb_phone;
    }

    public function get_comments()
    {
        return $this->comments;
    }

    public function get_moc_attendance()
    {
        return $this->moc_attendance;
    }

    public function backed(FormStateInterface $form_state)
    {
        $this->set_values($form_state);
        $this->virtual_in_person();
        if ($this->get_meeting_id() == null) {
            $status = $this->deduplication();
        } else {
            $status = $this->update_meeting_info();
        }
        $this->redirect($status);
    }

    public function set_values(FormStateInterface $form_state)
    {
        if ($form_state->getValue("meeting_value") == "new") {
            $this->member_id = $form_state->getValue("select_rep");
            $this->meeting_id = null;
            $this->nfb_contact = $form_state->getValue("nfb_civicrm_f_name_1") . " " . $form_state->getValue("nfb_civicrm_l_name_1");
        } else {
            $this->member_id = null;
            $this->meeting_id = $form_state->getValue("meeting_value");
            $this->nfb_contact = $form_state->getValue("nfb_civicrm_f_name_1") . " " . $form_state->getValue("nfb_civicrm_l_name_1");
        }
        $this->meeting_location = $form_state->getValue("meeting_location");
        $this->meeting_date = $form_state->getValue("meeting_day");
        $this->meeting_time = $form_state->getValue("meeting_time");
        $this->nfb_contact = $form_state->getValue("nfb_civicrm_f_name_1") . " " . $form_state->getValue("nfb_civicrm_l_name_1");
        $this->nfb_phone = $form_state->getValue("nfb_civicrm_phone_1");
        $this->moc_attendance = $form_state->getValue("attendance");
        $this->moc_contact = $form_state->getValue("moc_contact");
    }

    public function deduplication()
    {
        $this->database = new base();
        $year = date("Y");
        $query = "select * from nfb_washington_activities where member_id = '" . $this->get_member_id() . "' and  meeting_year = '" . $year . "';";
        $key = 'activity_id';
        $activity_id = null;
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $meeting) {
            $meeting = get_object_vars($meeting);
            if ($activity_id == null) {
                $activity_id = $meeting['activity_id'];
            }
        }
        if ($activity_id == null) {
            $this->insert_query();
            $status = "success";
        } else {
            $status = "duplicate";
        }
        return $status;
    }

    public function insert_query()
    {
        $year = date('Y');
        $this->database = new base();
        $feilds = array(
            "member_id" => $this->get_member_id(),
            "type" => "meeting",
            "meeting_date" => $this->get_meeting_date(),
            "meeting_time" => $this->get_meeting_time(),
            "description" => "Washington Seminar Meeting",
            "location" => $this->virtual_in_person(),
            "m_o_c_contact" => $this->get_moc_contact(),
            "nfb_contact" => $this->get_moc_contact(),
            "nfb_phone" => $this->get_nfb_phone(),
            "moc_attendance" => $this->get_moc_attendance(),
            "meeting_year" => $year,
            "created_user" => \Drupal::currentUser()->getUsername(),
            "last_modified_user" => \Drupal::currentUser()->getUsername(),
        );
        $table = "nfb_washington_activities";
        $this->database->insert_query($table, $feilds);
        $this->database = null;
    }
    public function  update_meeting_info()
    {
        $this->database = new base();
        $query = "update nfb_washington_activities
        set meeting_date = '".$this->get_meeting_date()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_activities
        set meeting_time = '".$this->get_meeting_time()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_activities
        set location = '".$this->get_meeting_location()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_activities
        set m_o_c_contact = '".$this->get_moc_contact()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_activities
        set moc_attendance = '".$this->get_moc_attendance()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_activities
        set nfb_contact = '".$this->get_nfb_contact()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_activities
        set nfb_phone = '".$this->get_nfb_phone()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_activities
        set last_modified_user = '".\Drupal::currentUser()->getUsername()."'
        where activity_id = '".$this->get_meeting_id()."';";
        $this->database->update_query($query);
        $this->database = null;
        $status = "update";
        return $status;
    }
    public function virtual_in_person()
    {
        return $this->get_meeting_location();
    }

    public function redirect($status)
    {
        if ($status == "duplicate")
        {
            $url = '/nfb-washington/meeting/'.$this->get_meeting_id();
            $message = "There is another meeting for this member of congress. Please update it below";
        }
        elseif($status == "success")
        {
            $url = "nfb-washington/home";
            $message = "Meeting scheduled";
        }
        else {
            $url = "nfb-washington/home";
            $message = "Meeting updated";
        }
        drupal_set_message($message);
        $ender = new RedirectResponse($url);
        $ender->send(); $ender = null;
        return;
    }
}