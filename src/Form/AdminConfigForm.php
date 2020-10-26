<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\verification\api_key_check;

class AdminConfigForm extends FormBase
{
    public $verification;
    public $factory;
    public function getFormId()
    {
        return "nfbwashadminconfig";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
       $this->verification = new api_key_check();
       $this->verification->api_key_validation($form, $form_state);

    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
}