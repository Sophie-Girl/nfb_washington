<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;

class  new_update_meeting_backend
{
    public $database;
    public $meeting_id;
    public $member_id;
    public $meeting_location;
    public  $meeting_time;
    public $meeting_date;
    public $moc_contact;
    public $nfb_contact;
    public $nfb_phone;
    public $comments;
    public $moc_attendance;
    public function get_meeting_id()
    {return $this->meeting_id;}
    public function get_meeting_location()
    {return $this->meeting_location;}
    public function get_meeting_time()
    {return $this->meeting_time;}
    public function get_meeting_date()
    {return $this->meeting_date;}
    public function get_moc_contact()
    {return $this->moc_contact;}
    public function get_nfb_contact()
    {return $this->nfb_contact;}
    public function  get_nfb_phone()
    {return $this->nfb_phone;}
    public function get_comments()
    {return $this->comments;}
    public function get_moc_attendance()
    {return $this->moc_attendance;}
    public function backed(FormStateInterface $form_state)
    {
        $this->set_values($form_state);
        if($this->get_meeting_id() == "null")
        {}
        else {

        }
    }
    public function set_values(FormStateInterface $form_state)
    {
        if($form_state->getValue("meeting_value") == "new")
        {
            $this->member_id = $form_state->getValue("select_rep");
            $this->meeting_id = null;
        }
        else {
            $this->member_id = null;
            $this->meeting_id = $form_state->getValue("meeting_value");
        }
        $this->meeting_location = $form_state->getValue("meeting_location");
        $this->meeting_date = $form_state->getValue("meeting_day");
        $this->meeting_time = $form_state->getValue("meeting_time");
        $this->nfb_contact = $form_state->getValue("nfb_civicrm_f_name_1")." ".$form_state->getValue("nfb_civicrm_l_name_1");
        $this->nfb_phone = $form_state->getValue("nfb_civicrm_phone_1");
        $this->moc_attendance = $form_state->getValue("attendance");
        $this->moc_contact = $form_state->getValue("moc_contact");
    }
    public function deduplication()
    {
        $this->database = new base();

    }
}
