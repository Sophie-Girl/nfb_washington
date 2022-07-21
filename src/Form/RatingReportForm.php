<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\report\rating_report;
use Drupal\nfb_washington\post_process\report\rating_report_backend;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\civicrm\Civicrm;
class  RatingReportForm extends FormBase
{
    public  $form_factory;
    public function getFormId()
    {
       return "nfb_wash_report_rate";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
       $this->form_factory = new rating_report();
       $this->form_factory->build_rating_form($form, $form_state);
       $this->form_factory = null;
       return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $civicrm = new Civicrm(); $civicrm->initialize();
        $civicrm_v4 = new civicrm_v4($civicrm);
        $this->form_factory = new rating_report_backend($civicrm_v4);
        $this->form_factory->backend_markups_and_array($form_state);



    }
}