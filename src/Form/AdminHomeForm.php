<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\admin\admin_home;
use Drupal\nfb_washington\verification\api_key_check;

class AdminHomeForm extends FormBase
{
    public $database;
    public $verification;
    public function getFormId()
    {
        return 'nfb_wash_admin_home';
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
       $this->verify_api_key($form, $form_state);
       $factory = new admin_home();
       $factory->build_form_markups($form, $form_state);
       return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
    public function verify_api_key(&$form, $form_state)
    {
        $this->verification = new api_key_check();
        $this->verification->api_key_validation($form,$form_state);
    }
}
