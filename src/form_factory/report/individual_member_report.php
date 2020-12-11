<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\civicrm\Civicrm;
use Drupal\nfb_washington\civicrm\civi_query;
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
    public $link_id;
    public function get_link_id()
    {return $this->link_id;}
    public $note_markup;
    public function get_note_markup()
    {return $this->note_markup;}
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
    public function get_contact_info()
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civi_query = new civi_query($civi);
        $this->civi_query->civi_entity = "Contact";
        $this->civi_query->civi_mode = 'get';
        $this->civi_query->civi_params = array(
            'sequential' => 1,
            'id' => $this->get_civicrm_id(),
        );
        $this->civi_query->civi_query();
        foreach($this->civi_query->get_civicrm_result() as $contact)
        {
            $this->first_name = $contact['first_name'];
            $this->last_name =  $contact['last_name'];
            $this->phone_number = $contact['phone'];
        }
        $this->civi_query = null;
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
                $note_markup = $note_markup."<p><a href='/nfb_washington/admin/notes/create' class='button-1' role ='button' aria-label='create new note'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Create New&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='/nfb_washington/admin/note_link' class='button-2' role='button' aria-label='Link Existing Note'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Link Existing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>";
            }
        }

    }
    public function note_loop(&$note_markup, $note_id, &$count)
    {

        $year = date('Y');
        $this->database = new base();
        $query = "select * from nfb_washington_note where note_id = '".$note_id."';";
        $key = "note_id";
        $this->database->select_query($query, $key);
        foreach($this->get_sql_result() as $note)
        { $note = get_object_vars($note);
        if($note['note_year'] == $year)
        {
         $note_markup = $note_markup. "<p>- ".$note['note']."</p>";
         if($this->get_user_permission() == "true")
         {
             $note_markup = $note_markup."<p><a href='/nfb_washington/admin/notes/create' class='button-1' role ='button' aria-label='create new note'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Create New&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='/nfb_washington/admin/note_link' class='button-2' role='button' aria-label='Link Existing Note'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Link Existing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href='/nfb_washington/admin/note/remove.".$this->get_link_id()."' class='button-3' role='button' aria-label='Remove note'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Remove This Note&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>";
         }
         $count++;
        }}
        $this->database = null;
    }
    public function relevant_committees_markup()
    {

    }



}
