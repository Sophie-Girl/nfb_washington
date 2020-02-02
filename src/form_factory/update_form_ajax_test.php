<?php
Namespace Drupal\nfb_washington\form_factory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\archive_nfb\representative_data;

class update_form_ajax_test extends markup_elements
{
    public function field_set(&$form, $form_state)
    {
        $form['meeting_data'] = array(
            '#prefix' => "<div id = 'data_wrapper'>",
            '#type' => 'fieldset',
            '#title' => $this->t("Meeting Information"),
            '#suffix' => "</div>",
        );
    }

    public function update_first_name(&$form, $form_state)
    {
        $this->representative_data = new representative_data();
        $category = 'nfb_contact_name';
        $this->representative_data->get_rep_data_update($form_state, $category, $text);
        $form['meeting_data']['nfb_contact_name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Contact Person Name"),
            '#size' => 20,
            '#required' => TRUE,
        );
        $this->representative_data = null;
    }
    public function update_MOC_contact(&$form, $form_state)
    {
        $this->representative_data = new representative_data();
        $category = 'lead_staff_name';
        $this->representative_data->get_rep_data_update($form_state, $category, $text);
        $form['meeting_data']['moc_contact'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Member of Congress Contact Person"),
            '#default_value' => $text,
            '#size' => 20,
            '#required' => false,
        );
        $this->representative_data = null;
    }
    public function update_contact_phone_record(&$form, $form_state)
    {
        $this->representative_data = new representative_data();
        $category = 'nfb_contact_phone';
        $this->representative_data->get_rep_data_update($form_state, $category, $text);
        $form['meeting_data']['nfb_civicrm_phone_1'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Contact Person Phone"),
            '#size' => 20,
            '#required' => TRUE,
        );
        $this->representative_data = null;
    }

    public function update_meeting_location(&$form, $form_state)
    {
        $this->representative_data = new representative_data();
        $category = 'activity_location';
        $this->representative_data->get_rep_data_update($form_state, $category, $text);
        $form['meeting_data']['meeting_location'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Meeting Location"),
            '#size' => 20,
            '#required' => TRUE,
        );
        $this->representative_data = null;
    }

    public function update_meeting_date(&$form, $form_state)
    {
        $form['meeting_data']['meeting_day'] = array(
            '#type' => 'date',
            '#title' => $this->t("Meeting Date"),
            '#format' => "'m/d/Y'",
            '#required' => TRUE,
            '#date_year_range' => '-0: +0',
            '#date_date_form' => "'m/d/Y'",
            '#min' => '2/1/2020',
            '#max' => '2/28/2020',
        );
        $this->representative_data = null;
    }

    public function meeting_time_update_element(&$form, $form_state)
    {
        $this->meeting_time_conversion($form_state, $text);
        $this->time_options();
        $form['meeting_data']['meeting_time'] = array(
            '#type' => 'select',
            '#title' => "Meeting Time",
            '#options' => $this->get_element_options(),
            '#required' => TRUE,
        );
    }

    public function update_expectend_attedence(&$form, $form_state)
    {
        $this->get_yes_no_text($form_state, $text);
        $form['meeting_data']['attendance'] = array(
            '#type' => 'select',
            '#title' => "Is this member attending the meeting?",
            '#options' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            '#required' => TRUE,
        );
    }

    public function get_yes_no_text(FormStateInterface $form_state, &$text)
    {
        $category = "contact_expected"; $this->representative_data = new representative_data();
        $this->representative_data->get_rep_data_update($form_state, $category, $text);
        if($text != "" && $text != "Error")
        {
            if($text == '1'){$text = "1";}
            else{$text = "0";}
        }
        else {$text ="";} $this->representative_data = null;
    }

    public function meeting_time_conversion($form_state, &$text)
    {
        $category = "activity_time"; $this->representative_data = new representative_data();
        $this->representative_data->get_rep_data_update($form_state, $category, $text);
        if($text != "Error" && $text != '')
        {
            if(strlen($text) == 4)
            {$hour = substr($text,0,2);
            $min = substr($text,2,2);}
            else {$hour = substr($text, 0,1);
            $min = substr($text,1,2);}
            if((int)$hour > 12)
            {$am_pm = "PM";
            $hour = (int)$hour - 12;
            }
            elseif($hour == "00")
            {$am_pm = "AM"; $hour = "12";}
            else {$am_pm = "AM";}
            $text = $hour.":".$min." ".$am_pm;
        }
        else {$text = '';}$this->representative_data = null;
    }

}