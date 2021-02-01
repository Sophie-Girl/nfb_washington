<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\report\individual_member_report;
use Drupal\nfb_washington\microsoft_office\html_to_word;
use Drupal\nfb_washington\post_process\report\ind_member_donwlaod;

class IndMemberReportForm extends FormBase
{
    public $form_factory;
    public $backend;
    public function getFormId()
    {
       return "wash_sem_ind_report";
    }

    public function buildForm(array $form, FormStateInterface $form_state, $member = "none")
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $form['#attached']['library'][] = 'nfb_washington/ind-report';
        $this->form_factory = new individual_member_report();
        if($member == "none")
        {$form['report_markup'] = array(
          '#type' => 'item',
          '#markup' => "<p>I shouldn't be accessible. Lol go away!</p>"
        );
        }
        else {$this->form_factory->build_report_page($form, $form_state, $member);}
        return $form;

    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $form_factory = new individual_member_report();
        $php_word = new html_to_word();
        $this->backend = new ind_member_donwlaod($php_word, $form_factory);
        $this->backend->backend($form_state);
    }
}