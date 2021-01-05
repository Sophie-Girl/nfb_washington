<?php
namespace Drupal\nfb_washington\email;
use Drupal\Core\Form\FormStateInterface;

class admin_notification extends base
{
    public function meeting_details(FormStateInterface $form_state, $params, $meeting_type)
    {
        $recipient_email = $params['staff_email'];
        $this->set_new_meeting_body($form_state, $params, $meeting_type);
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'nfb_washington';
        $key = 'nfb_washington_meeting_update';
        $to = $recipient_email;
        $send = true;
        $params['message'] = $this->get_body();
        $params['subject'] = "A Meeting Has Been created or Updated";
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, $send);
    }


    public function ratings_email_details(FormStateInterface $form_state, $params)
    {
        $this->set_ranking_email_body($form_state, $params);
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'nfb_washington';
        $key = 'nfb_wash_meeting_rating';
        $to = $params['staff_email'];
        $send = true;
        $params['message'] = $this->get_body();
        $params['subject'] = "A New Rating Has Been Submitted";
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, $send);
    }
    public function set_new_meeting_body(FormStateInterface $form_state, $params, $meeting_type)
    { $year = date('Y');
        $this->body = "
        A meeting for the ".$year." Washington seminar has been scheduled/updated
        The details are below".PHP_EOL."
        Elected Official: ".$params['rep_name'].PHP_EOL. "
        State: ".$params['state'].PHP_EOL."
        District/Seniority: ".$params['district'].PHP_EOL."
        Meeting Date: ".$form_state->getValue('meeting_day').PHP_EOL."
        Meeting Time: ".$form_state->getValue('meeting_time').PHP_EOL. "
        Meeting Location: ".$form_state->getValue('meeting_location').PHP_EOL."
        NFB Contact Person: ".$params['nfb_name'].PHP_EOL."
        NFB contact Phone Number: ".$form_state->getValue('nfb_civicrm_phone_1')."";
    }
    public function set_ranking_email_body(FormStateInterface $form_state, $params)
    {
        $this->body = "A rating has been submitted for ".$params['rep_name']." please see the details below.".PHP_EOL."
        State: ".$params['state'].PHP_EOL."
        Elected Official: ".$params['rep_name']. PHP_EOL."
        Issue 1 Rating: ".$form_state->getValue('issue_1_ranking'). PHP_EOL."
        Issue 1 Comment: ".$params['comment_1'].PHP_EOL."
        Issue 2 Rating: ".$form_state->getValue('issue_2_ranking').PHP_EOL."
        Issue 2 Comment: ".$params['comment_2'].PHP_EOL. "
        Issue 3 Rating: ".$form_state->getValue('issue_3_ranking').PHP_EOL."
        Issue 3 Comment:".$params['comment_3'].PHP_EOL."
        Person Reporting: ".$params['nfb_contact']." ".PHP_EOL."
        Reporting Person Phone: ".$form_state->getValue('nfb_civicrm_phone_1');
    }
}