<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\admin\admin_remove_note;
class AdminRemoveNoteForm extends FormBase
{
    public $form_factory;
    public function  getFormId()
    {
        return  "nfb_wash_remove_notes_from_member ";
    }
    public function  buildForm(array $form, FormStateInterface $form_state, $link = 'na')
    {
        $this->form_factory = new admin_remove_note();
        $this->form_factory->build_form_array($form, $form_state, $link);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
}