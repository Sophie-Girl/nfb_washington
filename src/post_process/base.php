<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_civicrm_bridge\civicrm\processes;
use Drupal\nfb_civicrm_bridge\civicrm\query;
use Drupal\nfb_civicrm_bridge\data_repo\repo;
use Drupal\nfb_washington\archive_nfb\activity_data;
use Drupal\nfb_washington\email\admin_notification;

class base {
    public $civi_repo;
    public $civi_bridge;
    public $civi_query;
    public $archive_nfb;
    public $email;
    public function dependency_injection(FormStateInterface $form_state)
    {
        $civicrm = new Civicrm();
        $civicrm->initialize();
        $this->civi_query = new query($civicrm);
        $this->civi_repo = new repo($form_state);
        $this->civi_bridge = new processes($this->civi_query, $this->civi_repo);
        $this->archive_nfb = new activity_data();
    }
    public function set_email_meeting_body(FormStateInterface $form_state, $params)
    {
        $this->email = new admin_notification();
        $this->email->meeting_details($form_state, $params);
        $this-> email = null;
    }
    public function set_ranking_email_body(FormStateInterface $form_state, $params)
    {
        $this->email = new admin_notification();
        $this->email->ratings_email_details($form_state, $params);
        $this->email = null;
    }
}