<?php
namespace Drupal\nfb_washington\email;
use Drupal\nfb_washington\database\base;

class email_base
{
    public $body;
    public $subject;
    public $database;
    public function get_body()
    {
        return $this->body;
    }

    public function get_subject()
    {
        return $this->subject;
    }
    public function get_staff_email()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_config where setting = 'staff_email';";
        $key = "value";
        $this->database->select_query($query, $key);
        $staff_email = null;
        foreach($this->database->get_result() as $config)
        {
            $config = get_object_vars($config);
            if($staff_email == null)
            {
                $staff_email = $config['value'];
            }
        }
        return $staff_email;
    }
}