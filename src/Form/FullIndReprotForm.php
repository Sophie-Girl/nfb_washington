<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\report\all_ind_report;

class FullIndReprotForm extends FormBase
{
    public $form_factory;
    public function getFormId()
    {
        return "nfb_washington_all_mem_report";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $this->form_factory = new all_ind_report();
        $this->form_factory->build_form_array($form, $form_state);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
}