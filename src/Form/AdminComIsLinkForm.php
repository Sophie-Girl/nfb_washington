<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\verification\api_key_check;
use Drupal\nfb_washington\verification\congress_number_check;

class  adminComIsLinkForm extends  FormBase
{
    public  $verification;

    public function getFormId()
    {
        // TODO: Implement getFormId() method.
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // TODO: Implement buildForm() method.
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
    public function congress_number_markup(&$form, &$form_state)
    {
        $this->verification = new congress_number_check();
        $this->verification->congress_number_verification($form, $form_state);
        $this->verification = null;
    }
}