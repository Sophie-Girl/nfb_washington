<?php
Namespace Drupal\nfb_washington\civicrm;
use Drupal\civicrm\Civicrm;

class civi_query
{
    public $civi_entity;
    public $civi_mode;
    public $civi_params;
    public $civi_result;
    public $civicrm;
    public function get_civi_entity()
    {return $this->civi_entity;}
    public function get_civicrm_mode()
    {return $this->civi_mode;}
    public function get_civicrm_params()
    {return $this->civi_params;}
    public function get_civicrm_result()
    {return $this->civi_result;}
    public function __construct(Civicrm $civicrm)
    {
        $this->civicrm = $civicrm;
        // connell, sophie: Needs to an initialized instance. Plese do Civicrm->initialize() before sending the class instance here
    }
    public function civi_query()
    {
        $this->civi_result  = civicrm_api3($this->get_civi_entity(), $this->get_civicrm_mode(), $this->get_civicrm_params());
    }

}