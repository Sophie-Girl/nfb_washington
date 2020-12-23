<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\report\meeting_report;

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
        // TODO: Implement submitForm() method.
    }
    public function data_refresh(&$form, $form_state)
    {
        \Drupal::logger("fuck_this_shit")->notice("I fuc,king hate this project and my life");
        return $form['report_markup'];
    }
}