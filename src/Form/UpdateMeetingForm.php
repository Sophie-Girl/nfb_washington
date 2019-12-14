<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\nfb_washington\form_factory\form_factory;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
class UpdateMeetingForm extends FormBase
{
    private $form_factory;
    public function getFormId()
    {
        return 'wash_sem_update_meeting';
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
       $this->form_factory = new form_factory();
       $this->form_factory->build_update_meeting_form($form);
       return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
    }
}