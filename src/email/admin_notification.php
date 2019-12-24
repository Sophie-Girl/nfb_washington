<?php
namespace Drupal\nfb_washington\email;
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
}