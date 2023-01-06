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
        else {
            $this->result = "error";
        }
    }
    public function update_query($query)
    {
        $this->database = \Drupal::database();
        \Drupal::logger("Kevin_mccarthy_sucks")->notice("query: ".$query);
        $this->result =  $this->database->query($query)->execute();
    }
    public function insert_query($table, $fields)
    {
        $this->database = \Drupal::database();
        $this->result = $this->database->insert($table)->fields($fields)->execute();
    }
}