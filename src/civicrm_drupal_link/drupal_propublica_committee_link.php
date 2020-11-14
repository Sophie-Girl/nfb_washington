<?php
Namespace Drupal\nfb_washington\civicrm_drupal_link;
use Drupal\nfb_washington\propublica\committee;

class drupal_propublica_committee_link
{
    public $propublica;
    public function __construct(committee $committee)
    {
        $this->propublica = $committee;
    }
}