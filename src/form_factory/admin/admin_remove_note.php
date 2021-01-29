<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\nfb_washington\database\base;
class  admin_remove_note
{
    public $database;
    public $note_link_id;
    public function get_note_link_id()
    {return $this->note_link_id;}
}