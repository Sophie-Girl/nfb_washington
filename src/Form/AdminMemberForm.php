<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civi_query;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\civicrm_drupal_link\drupal_member_civi_contact_link;
use Drupal\nfb_washington\form_factory\admin\admin_members;
use Drupal\nfb_washington\post_process\admin\admin_member_backend;
use Drupal\nfb_washington\propublica\members;
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
        if($form_state->getValue("members_mode") == "initial_upload")
        {$mode = "new_congress";}
        else {$mode = "maint";}
       $this->form_backend($mode);
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
    public function markup_refresh(array &$form, FormStateInterface $form_state)
    {
        return $form['mode_explain'];
    }
    public function form_backend($mode)
    {
        $civicrm = new Civicrm(); $civicrm->initialize();
        $civi_query = new civicrm_v4($civicrm);
        $propulbica = new members();
        $link = new drupal_member_civi_contact_link($civi_query, $propulbica);
        $backend = new admin_member_backend($link);
        $backend->backend($mode);

    }
}