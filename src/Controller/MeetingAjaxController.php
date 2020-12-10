<?php

Namespace Drupal\nfb_washington\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MeetingAjaxController extends ControllerBase
{
    public $database;
    public $civicrm;
    public $meeting_id;
    public function  get_meeting_id()
    {return $this->meeting_id;}
    public $data;
    public function get_data()
    {return $this->data;}
    public $sql_result;
    public function get_sql_result()
    {return $this->sql_result;}
    public $member_id;
    public function get_member_id()
    {return $this->member_id;}
    public $civicrm_id;
    public function get_civicrm_id()
    {return $this->civicrm;}
    public $nfb_contact;
    public function get_nfb_contact()
    {return $this->nfb_contact;}
    public $nfb_phone;
    public function get_nfb_phone()
    {return $this->nfb_phone;}
    public $meeting_location;
    public function  get_meeting_location()
    {return $this->meeting_location;}
    public $meeting_time;
    public function get_meeting_time()
    {return $this->meeting_time;}
    public $meeting_date;
    public function get_meeting_date()
    {return $this->meeting_date;}
    public $moc_contact;
    public function get_moc_contact()
    {return $this->moc_contact;}
    public $moc_attendance;
    public function get_moc_attendance()
    {return $this->moc_attendance;}
    public function content()
    {
        $this->set_Data();
        \drupal::logger("nfb_washington_ajax")->notice(print_r($this->get_data(),true));
        return new JsonResponse($this->get_data());
    }
    public function request_js_data()
    {
        $request = Request::createFromGlobals();
        $this->meeting_id = $request->request->get('meetingid');
    }
    public function find_meeting()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_activities where activity_id = '".$this->get_meeting_id()."';";
        $key = 'activity_id';
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
        $this->database = null;
    }
    public function meeting_data_info_for_loop()
    {
        foreach($this->get_sql_result() as $meeting)
        {
            $meeting = get_object_vars($meeting);
            $this->meeting_location = $meeting['location'];
            $this->meeting_time = $meeting['meeting_time'];
            $this->meeting_date = $meeting['meeting_date'];
            $this->nfb_contact = $meeting['nfb_contact'];
            $this->nfb_phone = $meeting['nfb_phone'];
            $this->moc_contact = $meeting['m_o_c_contact'];
            $this->moc_attendance = $meeting['moc_attendance'];
        }
    }
    public function convert_y_n()
    {
        if($this->get_moc_attendance() == '0')
        { $this->moc_attendance = "Yes";}
        else {$this->moc_attendance = "No";}
    }
    public function build_data_array()
    {
        $data[0] = $this->get_meeting_location();
        $data[1] = $this->get_meeting_time();
        $data[2] = $this->get_meeting_date();
        $data[3] = $this->get_nfb_contact();
        $data[4] = $this->get_nfb_phone();
        $data[5] = $this->get_moc_contact();
        $data[6] = $this->get_moc_attendance();
        $this->data = $data;
    }
    public function set_Data()
    {
        $this->request_js_data();
        $this->find_meeting();
        $this->meeting_data_info_for_loop();
        $this->convert_y_n();
        $this->build_data_array();
    }



}

