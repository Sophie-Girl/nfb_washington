<?php
Namespace Drupal\nfb_washington\verification;
class issue_count_check extends api_key_check
{
    public $issue_count;
    public function get_issue_count()
    {return $this->issue_count;}
}