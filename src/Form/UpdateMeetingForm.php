<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\form_factory;
use Drupal\nfb_washington\post_process\meeting_backend;

class UpdateMeetingForm extends FormBase
{
    private $form_factory;
    private $post_process;
    public function getFormId()
    {
        return 'wash_sem_update_meeting';
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
       $this->form_factory = new form_factory();
       $this->form_factory->build_update_meeting_form($form, $form_state);
       $this->form_factory = null;
       return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
       $this->post_process =  new meeting_backend();
       $this->post_process->meeting_person($form_state);
       $this->post_process = null;
       drupal_set_message($this->t("Meeting Updated"), 'status');
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
    }
    public function staterep_refresh(&$form, $form_state)
    {
        return $form['select_rep'];
    }
    public function data_refresh(&$form,$form_state)
    {
        $form_state['nfb_civicrm_f_name_1']['#value'] = "Test";
    }
}