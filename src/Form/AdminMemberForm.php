<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\verification\api_key_check;

class AdminMemberForm extends FormBase
{
    public $verification;
    public function  getFormId()
    {
        return "nfb_wash_admin_member_form";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $this->api_verification($form, $form_state);

    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
    public function api_verification(&$form, $form_state)
    {
        $this->verification = new api_key_check();
        $this->verification->api_key_validation($form, $form_state);
    }
}