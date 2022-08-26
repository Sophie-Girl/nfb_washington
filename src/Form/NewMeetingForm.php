<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\form_factory;
use Drupal\nfb_washington\post_process\new_update_meeting_backend;

class NewMeetingForm extends FormBase
{
    public $form_factory;
    public $post_process;
    public function getFormId()
    {return 'washington_sem_new_meeting';}
    public function buildForm(array $form, FormStateInterface $form_state, $meeting = "new")
    {
        $form['#attached']['library'][] = 'nfb_washington/updatemeeting';
        $this->form_factory = new form_factory();
        $this->form_factory->build_new_meeting_time($form, $form_state, $meeting);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->post_process = new new_update_meeting_backend();
        \Drupal::logger("nfb_washington")->notice("New/Edit Meeting form submission created by ".\Drupal::currentUser()->getAccountName());
        $this->post_process->backed($form_state);

    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
        $this->prevent_links($form_state);
        $this->prevent_carrots($form_state);
        $this->and_check($form_state);
    }
    public function staterep_refresh(&$form, $form_state)
    {
        return $form['select_rep'];
    }
    public function prevent_links(FormStateInterface $form_state)
    {
        $check = $form_state->getValue("meeting_location");
        $this->slash_check($check, $form_state);
        $this->com_check($check, $form_state);
        $this->https_check($check, $form_state);
    }
    public function slash_check($check, FormStateInterface $form_state)
    {
        $slash = strpos(" ".$check, "/");
        if($slash > 0)
        {
            $form_state->setErrorByName("meeting_location", "/ is not a valid character. If you are posting Zoom meeting information please use the meeting ID not join link");
        }
    }
    public function com_check($check, FormStateInterface $form_state)
    {
        $com = strpos(" ".$check, ".com");
        if($com > 0)
        {
            $form_state->setErrorByName("meeting_location", "If you are posting Zoom meeting information please use the meeting ID not join link");
        }
    }
    public function https_check($check, FormStateInterface  $form_state)
    {
        $https = strpos(" ".$check, "http");
        if($https > 0)
        {
            $form_state->setErrorByName("meeting_location", "If you are posting Zoom meeting information please use the meeting ID not join link");
        }
    }
    public function prevent_carrots(FormStateInterface  $form_state)
    {
        $check = $form_state->getValue("moc_contact");
        $carrot = strpos(" ".$check, "<");
        $close_carrot = strpos(" ".$check, ">");
        if($carrot > 0)
        {
            $form_state->setErrorByName("moc_contact", "Illegal character choice in <. Please remove");
        }
        if($close_carrot > 0 )
        {
            $form_state->setErrorByName("moc_contact", "Illegal character choice in >. Please remove");
        }
        $check = $form_state->getValue("meeting_location");
        $carrot = strpos(" ".$check, "<");
        $close_carrot = strpos(" ".$check, ">");
        if($carrot > 0)
        {
            $form_state->setErrorByName("meeting_location", "Illegal character choice in <. Please remove");
        }
        if($close_carrot > 0 )
        {
            $form_state->setErrorByName("meeting_location", "Illegal character choice in >. Please remove");
        }
    }
    public function and_check(FormStateInterface  $form_state)
    {
        $check = $form_state->getValue("moc_contact");
        $carrot = strpos(" ".$check, "&");

        if($carrot > 0)
        {
            $form_state->setErrorByName("moc_contact", "Illegal character choice in &. Please remove");
        }
        $check = $form_state->getValue("meeting_location");
        $carrot = strpos(" ".$check, "&");
        if($carrot > 0)
        {
            $form_state->setErrorByName("meeting_location", "Illegal character choice in &. Please remove");
        }

    }


}