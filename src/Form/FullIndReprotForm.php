<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\form_factory\report\all_ind_report;
use Drupal\nfb_washington\form_factory\report\individual_member_report;
use Drupal\nfb_washington\microsoft_office\html_to_word;
use Drupal\nfb_washington\post_process\report\all_member_download;

class FullIndReprotForm extends FormBase
{
    public $form_factory;
    public $backend;
    public function getFormId()
    {
        return "nfb_washington_all_mem_report";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $this->form_factory = new all_ind_report();
        $this->form_factory->build_form_array($form, $form_state);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $form_factory = new individual_member_report();
        $php_word = new html_to_word();
        $civicrm = new Civicrm(); $civicrm->initialize();
        $civicrm_v4 = new civicrm_v4($civicrm);
        $this->backend = new all_member_download($php_word, $form_factory, $civicrm_v4);
        $this->backend->full_backend();
    }
}