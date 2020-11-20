<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\admin\admin_com_member;
use Drupal\nfb_washington\post_process\admin\admin_committee_backend;
use Drupal\nfb_washington\verification\api_key_check;
use Drupal\nfb_washington\verification\congress_number_check;

class  AdminComMemForm extends FormBase
{
    public $verification;
    public $factory;
    public $backend;
    public function getFormId()
    {
        return "nfb_washington_con_mem";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $this->verify_api_key($form, $form_state);
        $this->congress_number_markup($form, $form_state);
        $this->factory = new admin_com_member();
        $this->factory->build_form_array($form, $form_state);
        $this->factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->backend = new admin_committee_backend();
        $this->backend->member_backend($form_state);
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