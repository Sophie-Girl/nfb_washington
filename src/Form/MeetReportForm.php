<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\report\meeting_report;
use Drupal\nfb_washington\microsoft_office\html_to_word;

class MeetReportForm extends FormBase
{
    public $form_factory;
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
        $this->form_factory = new meeting_report();
        $this->form_factory->begin_new_download_markup();
        $text = $this->form_factory->get_markup();
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