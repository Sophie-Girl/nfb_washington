<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\form_factory;

class HomeSemForm extends FormBase
{
    private $form_factory;
    public function getFormId()
    {
        return 'wash_sem_admin_home';
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $form['#attached']['library'][] = 'nfb_washington/ease-of-use';
        $this->form_factory = new form_factory();
        $this->form_factory->build_home_page_form($form, $form_state);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
    public function refresh_meeting(&$form, $form_state)
    {
        return $form['meeting_info_markup'];
    }
}