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
}