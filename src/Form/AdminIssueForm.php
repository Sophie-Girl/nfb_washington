<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\admin\admin_issue;
use Drupal\nfb_washington\post_process\admin\admin_issue_backend;
use Drupal\nfb_washington\verification\api_key_check;
use Drupal\nfb_washington\verification\congress_number_check;

class AdminIssueForm extends FormBase
{
    public $verification;
    public $form_factory;
    public $backend;
    public function getFormId()
    {
       return "nmfb_washington_issue_admin";
    }
    public function  buildForm(array $form, FormStateInterface $form_state, $issue = "create")
    {
        $this->verify_api_key($form, $form_state);
        $this->congress_number_markup($form, $form_state);
        $this->form_factory = new admin_issue();
        $this->form_factory->issue_switch($issue, $form, $form_state);
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",
        );
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $issue = $form_state->getValue("issue_value");
        $this->backend = new admin_issue_backend();
        $this->backend->issue_backend($issue, $form_state);
    }
    public function verify_api_key(&$form, $form_state)
    {
        $this->verification = new api_key_check();
        \drupal::logger('nfb_washington')->notice("i am about to run the query");
        $this->verification->api_key_validation($form,$form_state);
    }
    public function congress_number_markup(&$form, &$form_state)
    {
        $this->verification = new congress_number_check();
        $this->verification->congress_number_verification($form, $form_state);
        $this->verification = null;
    }

}