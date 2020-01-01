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
    { $year = date('Y'); $year = '2019'; echo $year; echo $state;
    $this->establish_connection();
    $test = $this->sql_connection->query("SELECT
        firstname, lastname, state, district, seminar_id
        FROM nfb_new.aaxmarwash_members where year = ".$year." and state = ".$state.";");
        if($test){
    $result = $test->fetch_all(MYSQLI_ASSOC);$this->sql_connection = null;}
        else {Echo "Bad Query"; Die;}
    }
    public function find_meeting_query($sem_id, &$array)
    {
        $year = date('Y');
        $this->establish_connection();
        $test = $this->sql_connection->query(
            "select activity_id, activity_date, activity_time from nfb_new.aaxmarwash_activities
    where 'year' = ".$year." and aseminiar_id =".$sem_id.";");
        $meeting = $test->fetch_all(MYSQLI_ASSOC);$this->sql_connection = null;
        if(!$meeting['0'])
        {$array[$sem_id]['meeting_id']= "";
         $array[$sem_id]['meeting_date'] = "";
         $array[$sem_id]['meeting_time'] = "";
        } else {
            $array[$sem_id]['meeting_id'] = $meeting['0']['activity_id'];
            $array[$sem_id]['meeting_date'] = $meeting['0']['activity_date'];
            $array[$sem_id]['meeting_time'] = $meeting['0']['activity_time'];
        }
    }
}