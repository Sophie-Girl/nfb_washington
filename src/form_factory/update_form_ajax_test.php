<?php
Namespace Drupal\nfb_washington\form_factory;
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
        $form['meeting_data']['nfb_civicrm_f_name_1'] = array(
          '#type' => 'textfield',
          '#title' => $this->t("Contact Person Name"),
          '#value' =>   $text,
          '#size' => 20,
          '#required' => TRUE,
        ); $this->representative_data = null;
    }
    public function meeting_time_update_element(&$form, $form_state)
    {
        $this->meeting_time_conversion($form_state, $text); $this->time_options();
        $form['meeting_data']['meeting_time'] = array(
            '#type' => 'select',
            '#title' => "Meeting Time",
            '#options' => $this->get_element_options(),
            '#default_value' => $text,
            '#required' => TRUE,
        );
    }

    public function meeting_time_conversion($form_state, &$text)
    {
        $category = "activity_time"; $this->representative_data = new representative_data();
        $this->representative_data->get_rep_data_update($form_state, $category, $text);
        if($text != "Error" && $text != 'N/A')
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
        else {$text = "";}$this->representative_data = null;
    }
}