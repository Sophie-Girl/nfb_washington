<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\admin\admin_members;
use Drupal\nfb_washington\verification\api_key_check;
use Drupal\nfb_washington\verification\congress_number_check;

class AdminMemberForm extends FormBase
{
    public $verification;
    public $factory;
    public function  getFormId()
    {
        return "nfb_wash_admin_member_form";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $this->api_verification($form, $form_state);
        $this->congress_number_markup($form, $form_state);
        $this->factory = new admin_members();
        $this->factory->build_form_array($form, $form_state);
        $this->factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
    public function api_verification(&$form, &$form_state)
    {
        $this->verification = new api_key_check();
        $this->verification->api_key_validation($form, $form_state);
        $this->verification = null;
    }
    public function congress_number_markup(&$form, &$form_state)
    {
        $this->verification = new congress_number_check();
        $this->verification->congress_number_verification($form, $form_state);
        $this->verification = null;
    }
    public function markup_refresh(&$form, $form_state)
    {
        \Drupal::logger("nfb_Washington_ajax")->notice("I am firing");
        return$form['mode_explain'];
    }
}