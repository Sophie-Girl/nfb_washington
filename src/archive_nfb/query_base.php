<?php
Namespace Drupal\nfb_washington\archive_nfb;
class query_base
{
    public $query_mode; // insert,update, select, etc.
    public $query_from; // from including joins
    public $query_where; // where clause
    public $sql_connection; // sql connection to archive.nfb.org
    public function get_query_mode()
    {return $this->query_mode;}
    public function get_query_from()
    {return $this->query_from;}
    public function get_query_where()
    {return $this->query_where;}
    public function get_sql_connection()
    {return $this->sql_connection;}
    public function establish_connection()
    {
        $this->credentials($servername, $username, $password);
        $this->sql_connection = new  \mysqli;
        $this->sql_connection->connect($servername, $username, $password, 'nfb_new');

    }
    public function credentials(&$servername, &$username, &$password)
    {
        $servername = '10.10.10.55:3306';
        $username = 'sconnell';
        $password = 'E7kr0129M!';
    }
    public function get_house_rep_for_state($state, &$result)
    { $year = date('Y');
    $this->establish_connection();
    $query = "SELECT
        firstname, lastname, state, district, seminar_id
        FROM nfb_new.aaxmarwash_members where year = '".$year."' and state = '".$state."' and in_office != '0';";
    $test = $this->sql_connection->query($query);
        if($test){
    $result = $test->fetch_all(MYSQLI_ASSOC);$this->sql_connection = null;
        unset($test);}
        else {Echo "Bad Query"; Die;}
    }
    public function find_meeting_query($sem_id, &$array)
    {
        $year = date('Y');
        $this->establish_connection();
        $query =  "select activity_id, activity_date, activity_time, activity_location, lead_staff_name, nfb_contact_name, nfb_contact_phone, contact_expected from nfb_new.aaxmarwash_activities
    where year = '".$year."' and aseminar_id = '".$sem_id."';";
        $test = $this->sql_connection->query($query);
        if($test){
        $meeting = $test->fetch_all(MYSQLI_ASSOC);$this->sql_connection = null;
            $array[$sem_id]['meeting_id'] = $meeting['0']['activity_id'];
            $array[$sem_id]['meeting_date'] = $meeting['0']['activity_date'];
            $array[$sem_id]['meeting_time'] = $meeting['0']['activity_time'];
            $array[$sem_id]['meeting_location'] = $meeting['0']['activity_location'];
            $array[$sem_id]['contact_person'] = $meeting['0']['nfb_contact_name'];
            $array[$sem_id]['contact_phone'] =  $meeting['0']['nfb_contact_phone'];
            $array[$sem_id]['rep_expected'] = $meeting['0']['contact_expected'];
            $array[$sem_id]['moc_contact'] = $meeting['0']['lead_staff_name'];
            if($meeting['issue1'])
            {
                $array[$sem_id]['issue1'] =$meeting['0']['issue1'];
            }

            unset($meeting);}
         else {
             $array[$sem_id]['meeting_id']= "";
             $array[$sem_id]['meeting_date'] = "";
             $array[$sem_id]['meeting_time'] = "";
             $array[$sem_id]['meeting_location'] = "";
             $array[$sem_id]['contact_phone'] = '';
             $array[$sem_id]['contact_phone'] = "";
             $array[$sem_id]['rep_expected'] = '';
             $array[$sem_id]['moc_contact'] = "";
         } unset($test); $this->sql_connection = null;
    }
    public function get_issue_name($rank, &$issue, &$id)
    {
        $year = date('Y');
        $this->establish_connection();
        $test = $this->sql_connection->query("select issue, issue_id from nfb_new.aaxmarwash_issues
        where year = '".$year."' and rank = '".$rank."';");
        if($test){ $result = $test->fetch_all(MYSQLI_ASSOC);
        $issue = $result['0']['issue'];
        $id = $result['0']['issue_id'];} else{ $issue = "issue ".$rank;
        }

    }
    public function error_message()
    {
    }


}