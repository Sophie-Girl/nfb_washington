<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\report\directory;

class CongDirForm extends FormBase
{
    public $form_factory;
    public function getFormId()
    {
       return "cong_directory_report";
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
       $this->form_factory = new directory();
       $this->form_factory->build_directory_form($form, $form_state);
       $this->form_factory = null;
       return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->form_factory = new directory();
        $this->form_factory->directory_backend();
    }
    public function data_refresh(&$form, FormStateInterface $form_state)
    {
        return  $form['preview_markup'];
    }
}