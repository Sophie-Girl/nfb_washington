<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\form_factory\admin\admin_committee;
use Drupal\nfb_washington\post_process\admin\admin_committee_backend;
use Drupal\nfb_washington\verification\api_key_check;
use Drupal\nfb_washington\verification\congress_number_check;

class AdminComitForm extends FormBase
{
    public $verification;
    public $form_factory;
    public $backend;
    public $database;
    public function getFormId()
    {
        return "nfb_washington_admin_commit";
    }
    public function  buildForm(array $form, FormStateInterface $form_state, $committee = "add")
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $form['#attached']['library'][] = 'nfb_washington/edit-committee';
        $this->form_factory = new admin_committee();
        $this->verify_api_key($form, $form_state);
        $this->congress_number_markup($form, $form_state);
        $this->form_factory->build_committee_form($committee, $form, $form_state);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->backend = new admin_committee_backend();
        $this->backend->backend($form_state);
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
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
        if($form_state->getValue("committee_value") == "add") {
            $this->database = new base();
            $query = "select * from nfb_washington_committee where committee_name = '" . $form_state->getValue("committee_name") . "'
        and committee_id != '" . $form_state->getValue("committee_value") . "' and chamber = '" . $form_state->getValue("chamber") . "';";
            $key = "committee_id";
            $this->database->select_query($query, $key);
            $count = 0;
            foreach ($this->database->get_result() as $committee) {
                $count++;
            }
            if ($count > 0) {
                $form_state->setErrorByName("committee_name", "THis committee already exists");
            }
        }
    }
}