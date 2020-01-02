<?php
namespace Drupal\nfb_washington\email;
use Drupal\Core\Form\FormStateInterface;

class admin_notification extends base
{
    public function meeting_details($recipient_email)
    {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'nfb_washington';
        $key = 'meeting_update';
        $to = $recipient_email;
        $send = true;
        $params['message'] = $this->get_body();
        $params['subject'] = $this->get_subject();
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, $send);
    }
    public function ratings_email_details($recipient_email)
    {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'nfb_washington';
        $key = 'meeting_update';
        $to = $recipient_email;
        $send = true;
        $params['message'] = $this->get_body();
        $params['subject'] = $this->get_subject();
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, $send);
    }
    public function set_new_meeting_body(FormStateInterface $form_state)
    { $year = date('Y');
        $this->body = "
        A new meeting has been scheduled for the ".$year." Washington seminar. 
        The details are below";
    }
}