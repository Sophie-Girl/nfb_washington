<?php
Namespace Drupal\nfb_washington\archive_nfb;
use Drupal\Core\Form\FormStateInterface;

class activity_data extends representative_data
{
    public function find_rep_name($sem_id, &$rep_name)
    {
        $this->establish_connection();
        $year = date('Y');
        $test = $this->sql_connection->query("Select firstname, lastname from nfb_new.aaxmarwash_members where year = '" . $year . "' and seminar_id = '" . $sem_id . "';");
        if ($test) {
            $result = $test->fetch_all(MYSQLI_ASSOC);
            $this->sql_connection = null;
            $rep_name = $result['0']['firstname'] . " " . $result['0']['lastname'];
        } Else {
            $rep_name = "";
        }
    }

    public function params_switch(FormStateInterface $form_state, &$params)
    {
        \Drupal::logger('nfb_washington')->notice($form_state->getFormObject()->getFormId());
        switch ($form_state->getFormObject()->getFormId()) {
            case 'washington_sem_new_meeting':
                $this->new_meeting_query($params);
                break;
            case 'wash_sem_update_meeting':
                $this->update_meeting_query($params);
                break;
            case "wash_sem_issue_rank":
                $this->issue_rating_queries($params);
                break;

        }
    }

    public function new_meeting_query($params)
    {
        $this->establish_connection();
        $query = "insert into nfb_new.aaxmarwash_activities (year, uid, last_updated, activity_date, activity_time,
    activity_location, activity, contact_expected, lead_staff_name, lead_staff_email, nfb_contact_name, nfb_contact_phone, aseminar_id)
    values('" . $params['year'] . "','" . $params['uid'] . "','" . $params['update_date'] . "','" . $params['date'] . "','" . $params['time'] . "',
    '" . $params['location'] . "','" . $params['activity_name'] . "','" . $params['rep_attend'] . "','" . $params['staff_lead'] . "','" . $params['staff_email'] . "','" . $params['contact_name'] . "',
    '" . $params['contact_phone'] . "', '" . $params['seminar_id'] . "')";
        $result = $this->sql_connection->query($query);
        if (!$result) {
            \Drupal::logger('nfb_washington')->notice("Something Is wrong:");
        }
        $this->sql_connection = null;
        $this->link_file($params);
        $this->issue_link($params);
    }

    public function update_date_query(&$params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set last_update = '" . $params['update_date'] . "'
            where activity_id = '" . $params['meeting_id'] . "';";
        $result = $this->sql_connection->query($query);
        if (!$result) {
            \Drupal::logger('nfb_washington')->notice("Something Is wrong:");
        }

        $this->sql_connection = null;
    }

    public function update_meeting_query(&$params)
    {
        $this->update_meeting_day_query($params);
        $this->update_meeting_time_query($params);
        $this->update_meeting_location_query($params);
        $this->update_contact_person_query($params);
        $this->update_contact_phone($params);
        $this->update_date_query($params);
        $this->get_updated_rep_name($params);
        $this->update_attendance($params);
    }

    public function update_meeting_day_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set activity_date = '" . $params['date'] . "'
            where activity_id = '" . $params['meeting_id'] . "';";
        $result = $this->sql_connection->query($query);
        if (!$result) {
            \Drupal::logger('nfb_washington')->notice("Something Is wrong:");
        } else {
            \Drupal::logger('nfb_washington')->notice(print_r($result, true));
        }
        $this->sql_connection = null;
    }

    public function update_meeting_time_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set activity_time = '" . $params['time'] . "'
            where activity_id = '" . $params['meeting_id'] . "';";
        $this->sql_connection->query($query);

        $this->sql_connection = null;
    }

    public function update_meeting_location_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set activity_location = '" . $params['location'] . "'
            where activity_id = '" . $params['meeting_id'] . "';";
        $this->sql_connection->query($query);
        $this->sql_connection = null;
    }

    public function update_contact_person_query($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set nfb_contact_name = '" . $params['contact_name'] . "'
            where activity_id = '" . $params['meeting_id'] . "';";
        $this->sql_connection->query($query);
        $this->sql_connection = null;
    }

    public function update_contact_phone($params)
    {
        $this->establish_connection();
        $query = "update nfb_new.aaxmarwash_activities
            set nfb_contact_phone = '" . $params['contact_phone'] . "'
            where activity_id = '" . $params['meeting_id'] . "';";
        $this->sql_connection->query($query);
        $this->sql_connection = null;
    }

    public function get_updated_rep_name(&$params)
    {
        $this->establish_connection();
        $result = $this->sql_connection->query("select aseminar_id from nfb_new.aaxmarwash_activities
    where activity_id = '" . $params['meeting_id'] . "';");
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $sem_id = $data['0']['aseminar_id'];
            $this->find_rep_name($sem_id, $rep_name);
            $params['rep_name'] = $rep_name;
            $params['district'] = substr($sem_id, 2, 10);
        } else {
            $rep_name = '';
            $params['rep_name'];
            $params['rep_name'] = '';
            $params['district'] = '';
        }

        $this->sql_connection = null;
    }

    public function update_issue_1($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set issue1 = '" . $params['issue_1'] . "' where activity_id = '" . $params['meeting_id'] . "';");
        $this->sql_connection = null;
    }

    public function update_issue_2($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set issue2 = '" . $params['issue_2'] . "' where activity_id = '" . $params['meeting_id'] . "';");
        $this->sql_connection = null;
    }

    public function update_issue_3($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set issue3 = '" . $params['issue_3'] . "' where activity_id = '" . $params['meeting_id'] . "';");
        $this->sql_connection = null;
    }

    public function update_issue_1_comment($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set comment1 = '" . $params['comment_1'] . "' where activity_id = '" . $params['meeting_id'] . "';");
        $this->sql_connection = null;
    }

    public function update_issue_2_comment($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set comment2 = '" . $params['comment_2'] . "' where activity_id = '" . $params['meeting_id'] . "';");
        $this->sql_connection = null;
    }

    public function update_issue_3_comment($params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set comment3 = '" . $params['comment_3'] . "' where activity_id = '" . $params['meeting_id'] . "';");
        $this->sql_connection = null;
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
        $this->update_date_query($params);
        $this->update_contact_phone($params);
        $this->get_updated_rep_name($params);
    }

    public function update_attendance(&$params)
    {
        $this->establish_connection();
        $this->sql_connection->query("update nfb_new.aaxmarwash_activities
        set contact_expected = '" . $params['rep_attend'] . "' where activity_id = '" . $params['meeting_id'] . "';");
        $this->sql_connection = null;
    }

    public function get_rep_id_for_link(&$params)
    {
        $this->establish_connection();
        $year = date('Y');
        $test = $this->sql_connection->query("select member_id from nfb_new.aaxmarwash_members
    where year = '" . $year . "' and seminar_id = '" . $params['seminar_id'] . "';");
        if ($test) {
            $result = $test->fetch_all(MYSQLI_ASSOC);
            $params['legislator_id'] = $result['0']['member_id'];
        } else {
            $params['legislator_id'] = "null";
        }
        $this->sql_connection = null;
    }

    public function link_file(&$params)
    {
        $this->get_activity_idfor_link($params);
        $this->get_rep_id_for_link($params);
        $this->insert_link($params);
    }

    public function get_activity_idfor_link(&$params)
    {
        $this->establish_connection();
        $year = date('Y');
        $test = $this->sql_connection->query("select activity_id from nfb_new.aaxmarwash_activities
        where year = '" . $year . "' and aseminar_id = '" . $params['seminar_id'] . "';");
        if ($test) {
            $result = $test->fetch_all(MYSQLI_ASSOC);
            $params['activity_id'] = $result['0']['activity_id'];
        } else {
            $params['activity_id'] = "null";
        }
        $this->sql_connection = null;
    }

    public function insert_link(&$params)
    {
        $year = date('Y');
        if ($params['activity_id'] != "null" && $params['legislator_id'] != "null") {
            $this->establish_connection();
            $result = $this->sql_connection->query("insert into nfb_new.aaxmarwash_linkactivity (year, activity_id, table_name, table_id)
        values ('" . $year . "', '" . $params['activity_id'] . "', 'aaxmarwash_members', '" . $params['legislator_id'] . "');");
            if (!$result) {
                \Drupal::logger('nfb_washington')->notice("Something Is wrong:");
            }
        }
    }

    public function get_issue_id(&$params)
    {
        $rank = '1';
        $this->get_issue_name($rank, $isssue, $id);
        $params['issue_1_id'] = $id;
        $rank = '2';
        $this->get_issue_name($rank, $issue, $id);
        $params['issue_1_id'] = $id;
        $rank = '3';
        $this->get_issue_name($rank, $issue, $id);
        $params['issue_3_id'] = $id;
    }

    public function issue_link(&$params)
    {   $this->get_issue_id($params);
        $this->establish_connection();  $year = date("Y");
        $this->sql_connection->query("insert into nfb_new.aaxmarwash_linkissue (year, issue_id, table_name, table_id)
    value ('".$year."','".$params['issue_1_id']."','aaxmarwash_members', '".$params['member_id']."');");
        $this->sql_connection->query("insert into nfb_new.aaxmarwash_linkissue (year, issue_id, table_name, table_id)
    value ('".$year."','".$params['issue_2_id']."','aaxmarwash_members', '".$params['member_id']."');");
        $this->sql_connection->query("insert into nfb_new.aaxmarwash_linkissue (year, issue_id, table_name, table_id)
    value ('".$year."','".$params['issue_3_id']."','aaxmarwash_members', '".$params['member_id']."');");

    }

}