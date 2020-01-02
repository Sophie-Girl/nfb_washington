<?php
namespace Drupal\nfb_washington\email;
use Drupal\Core\Form\FormStateInterface;

class admin_notification extends base
{
    public function meeting_details(FormStateInterface $form_state, $params)
    {
        $recipient_email = $params['staff_email'];
        $this->set_new_meeting_body($form_state, $params);
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
    public function set_new_meeting_body(FormStateInterface $form_state, $params)
    { $year = date('Y');
        $this->body = "
        A new meeting has been scheduled for the ".$year." Washington seminar. 
        The details are below".PHP_EOL."
        Elected Official: ".$params['rep_name'].PHP_EOL. "
        State: ".$form_state->getValue('select_state').PHP_EOL."
        District/Seniority: ".$params['district'].PHP_EOL."
        Meeting Date: ".$form_state->getValue('meeting_date').PHP_EOL."
        Meeting Time: ".$form_state->getValue('meeting_time').PHP_EOL. "
        Meeting Location: ".$form_state->getValue('meeting_location').PHP_EOL."
        NFB Contact Person: ".$form_state->getValue('nfb_civicrm_f_name_1')." ".$form_state->getValue('nfb_civicrm_l_name_1').PHP_EOL."
        NFB contact Phone Number: ".$form_state->getValue('nfb_civicrm_phone_1')."";

    }
}