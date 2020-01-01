<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\nfb_civicrm_bridge\data_repo\repo;
use Drupal\civicrm\Civicrm;
use Drupal\nfb_civicrm_bridge\civicrm\processes;
use Drupal\nfb_civicrm_bridge\civicrm\query;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\archive_nfb\activity_data;
class base {
    public $civi_repo;
    public $civi_bridge;
    public $civi_query;
    public $archive_nfb;
    public function dependency_injection(FormStateInterface $form_state)
    {
        $civicrm = new Civicrm();
        $civicrm->initialize();
        $this->civi_query = new query($civicrm);
        $this->civi_repo = new repo($form_state);
        $this->civi_bridge = new processes($this->civi_query, $this->civi_repo);
        $this->archive_nfb = new activity_data();
    }
}