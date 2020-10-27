<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\admin\admin_config;
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
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
       $this->verification = new api_key_check(); $this->factory =  new admin_config();
       $this->verification->api_key_validation($form, $form_state);
       $this->factory->build_form_array($form, $form_state);
       return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
}