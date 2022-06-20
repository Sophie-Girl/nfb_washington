<?php
Namespace Drupal\nfb_washington\civicrm;
use Drupal\civicrm\Civicrm;
class civicrm_v4 {
    public $civicrm;
    public $civi_entity;
    public $civi_mode;
    public $civi_params;
    public function __construct(Civicrm $civicrm)
    {
        $civicrm->initialize();
        $this->civicrm = $civicrm;
    } // Dependency injection
    public function get_civi_entity()
    { return $this->civi_entity;}
    public function get_civi_mode()
    { return $this->civi_mode;}
    public function get_civi_params()
    { return $this->civi_params;}
    public function civi_query_v4()
    {
        $result = civicrm_api4($this->get_civi_entity(), $this->get_civi_mode(),
        $this->get_civi_params());
        return $result;
    }
}