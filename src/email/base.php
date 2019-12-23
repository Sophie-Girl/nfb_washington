<?php
namespace Drupal\nfb_washington\email;
class base
{
    public $body;
    public $subject;

    public function get_body()
    {
        return $this->body;
    }

    public function get_subject()
    {
        return $this->subject;
    }
}