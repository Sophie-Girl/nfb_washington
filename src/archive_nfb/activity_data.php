<?php
Namespace Drupal\nfb_washington\archive_nfb;
use Drupal\Core\Form\FormStateInterface;

class activity_data extends representative_data
{
    public function find_rep_name($sem_id, &$rep_name)
    {
        $this->establish_connection(); $year = '2019'; // date('Y');
        $test = $this->sql_connection->query("Select firstname, lastname from nfb_new.aaxmarwash_members where year = '".$year."' and seminar_id = '".$sem_id."';");
        if($test){$result = $test->fetch_all(MYSQLI_ASSOC);$this->sql_connection = null;
        $rep_name = $result['0']['firstname']." ".$result['0']['lastname'];}
        Else{ $rep_name = "";}
    }
    public function params_switch(FormStateInterface $form_state, $params)
    {
        switch ($form_state->getFormObject()->getFormId())
        {
            case 'washington_sem_new_meeting':
                $this->new_meeting_query($params);
                break;
            case 'wash_sem_update_meeting':
                $this->update_meeting_query($params);
                break;
            case "wash_sem_issue_rank":
                break;
        }
    }

    public function new_meeting_query($params)
    {
        $this->establish_connection();
        $query = "insert into nfb_new.aaxmarwash_activities (year, uid, last_updated, activity_date, activity_time,
    activity_location, activity, lead_staff_name, lead_staff_email, nfb_contact_name, nfb_contact_phone, aseminar_id)
    values('".$params['year']."','".$params['uid']."','".$params['update_date']."','".$params['date']."','".$params['time']."',
    '".$params['location']."','".$params['activity_name']."','".$params['staff_lead']."','".$params['staff_email']."','".$params['contact_name']."',
    '".$params['contact_phone']."', '".$params['seminar_id']."')";
       $result =  $this->sql_connection->query($query);
    }

    public function update_meeting_query(&$params)
    {
        $this->update_meeting_day_query($params);
        $this->update_meeting_time_query($params);
        $this->update_meeting_location_query($params);
        $this->update_contact_person_query($params);
        $this->update_contact_phone($params);
       $this->get_updated_rep_name($params);
    }
    public function update_meeting_day_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set activity_date = '".$params['date']."'
            where activity_id = '".$params['meeting_id']."'";
        $this->sql_connection->query($query);
    }
    public function update_meeting_time_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set activity_time = '".$params['time']."'
            where activity_id = '".$params['meeting_id']."'";
        $this->sql_connection->query($query);
    }
    public function update_meeting_location_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set activity_location = '".$params['location']."'
            where activity_id = '".$params['meeting_id']."'";
        $this->sql_connection->query($query);
    }
    public function update_contact_person_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set nfb_contact_name = '".$params['contact_name']."'
            where activity_id = '".$params['meeting_id']."'";
        $this->sql_connection->query($query);
    }
    public function update_contact_phone($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set nfb_contact_phone = '".$params['contact_phone']."'
            where activity_id = '".$params['meeting_id']."'";
        $this->sql_connection->query($query);
    }
    public function get_updated_rep_name(&$params)
    {
        $this->establish_connection();
        $result =$this->sql_connection->query("select aseminar_id from nfb_new.aaxmarwash_activities
    where activity_id = '".$params['meeting_id']."';");
        if($result){
            $sem_id = $result['0']['aseminar_id'];
            $this->find_rep_name($sem_id, $rep_name);
            $params['rep_name'] = $rep_name;
        }
        else {$rep_name = '';}
    }
    public function update_issue_1($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set issue1 = '".$params['issue_1']."' where activity_id = '".$params['meeting_id']."';");
    }
    public function update_issue_2($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set issue2 = '".$params['issue_2']."' where activity_id = '".$params['meeting_id']."';");
    }
    public function update_issue_3($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set issue3 = '".$params['issue_3']."' where activity_id = '".$params['meeting_id']."';");
    }
    public function update_issue_1_comment($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set comment1 = '".$params['comment_1']."' where activity_id = '".$params['meeting_id']."';");
    }
    public function update_issue_2_comment($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set comment2 = '".$params['comment_2']."' where activity_id = '".$params['meeting_id']."';");
    }
    public function update_issue_3_comment($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set comment3 = '".$params['comment_3']."' where activity_id = '".$params['meeting_id']."';");
    }
    public function issue_rating_queries(&$params)
    {
        $this->update_issue_1($params);
        $this->update_issue_2($params);
        $this->update_issue_3($params);
        $this->update_issue_1_comment($params);
        $this->update_issue_2_comment($params);
        $this->update_issue_3_comment($params);
        $this->update_contact_person_query($params);
        $this->update_contact_phone($params);
    }

}