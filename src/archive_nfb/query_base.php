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
        $test =  $this->sql_connection->query("SELECT * FROM nfb_new.aaxmarwash_activities
where year = '2019';");
        print_r($test->fetch_all(MYSQLI_ASSOC));

    }
    public function credentials(&$servername, &$username, &$password)
    {
        $servername = '10.10.10.55:3306';
        $username = 'sconnell';
        $password = 'E7kr0129M!';
    }
}