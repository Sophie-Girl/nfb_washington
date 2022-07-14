<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\form_factory\report\meeting_report;
use Drupal\nfb_washington\microsoft_office\html_to_word;
use Drupal\nfb_washington\post_process\report\meeting_report_backend;
class MeetReportForm extends FormBase
{
    public $form_factory; public $backend;
    public function getFormId()
    {
        return 'meeting_report_nfb_washington';
    }
    public function  buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $this->form_factory = new meeting_report();
        $this->form_factory->build_form($form, $form_state);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $civicrm = new Civicrm(); $civicrm->initialize();
        $civicrm_v4 = new civicrm_v4($civicrm);
        $this->backend = new meeting_report_backend($civicrm_v4);
        $this->backend->begin_new_download_markup($form_state);
        $text = $this->backend->get_markup();
        $word  = new html_to_word();
        $word->report_name = "washington_seminar_meeting_report.docx";
        $word->font_size = '12';
        $word->download_doc($text);
    }
    public function report_refresh(&$form, $form_state)
    {
        return $form['report_markup'];
    }
}