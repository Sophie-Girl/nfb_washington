<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\admin\admin_issue_home;
use Drupal\nfb_washington\verification\api_key_check;
use Drupal\nfb_washington\verification\congress_number_check;

class AdminIssueHomeForm extends FormBase
{
    public $verification;
    public $form_factory;
    public function getFormId()
    {
        return "nfb_washington_admin_issue_home";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
        {
            $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
            $this->verify_api_key($form, $form_state);
            $this->congress_number_markup($form, $form_state);
            $this->form_factory = new admin_issue_home();
            $this->form_factory->Build_issue_home_form($form, $form_state);
            return $form;
        }
    public function submitForm(array &$form, FormStateInterface $form_state)
        {
    // TODO: Implement submitForm() method.
            // lol this form has no submit
        }
    public function verify_api_key(&$form, $form_state)
    {
        $this->verification = new api_key_check();
        $this->verification->api_key_validation($form,$form_state);
    }
    public function congress_number_markup(&$form, &$form_state)
    {
        $this->verification = new congress_number_check();
        $this->verification->congress_number_verification($form, $form_state);
        $this->verification = null;
    }
}