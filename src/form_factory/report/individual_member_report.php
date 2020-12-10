<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\nfb_washington\database\base;

class individual_member_report
{
    public $database;
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
    public function build_markup($member)
    {
        $this->member_id = $member;
        $this->set_user_permission();

    }
    public function member_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_members where member_id = '".$this->get_member_id()."';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
        $this->database = null;
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


}
