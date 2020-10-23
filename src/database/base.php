<?php
Namespace Drupal\nfb_washington\database;
class base
{
    public $database;
    public $result;
    public function get_result()
    {return $this->result;}
    public function select_query($query, $key)
    {
        $this->database = \Drupal::database();
        $sql = $this->database->query($query);
        if($sql)
        {
            $this->result = $sql->fetchAllAssoc($key);
        }
    }
}