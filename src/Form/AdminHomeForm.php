<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
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
        \drupal::logger('nfb_washington')->notice("i get to the form");
       $this->verify_api_key($form, $form_state);
       if($this->verification->get_status() == "false")
       {
           return $form;
       }
       else {
           //todo build form
       }
       return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
    public function verify_api_key(&$form, $form_state)
    {
        $this->verification = new api_key_check();
        \drupal::logger('nfb_washington')->notice("i am about to run the query");
        $this->verification->api_key_validation($form,$form_state);
    }
}
