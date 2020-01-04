<?php
Namespace Drupal\nfb_washington\form_factory;
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
        if($form_state->getValue('select_rep') != ""){ $text = "It Works!";}
        else {$text = "";}
        $form['meeting_data']['nfb_civicrm_f_name_1'] = array(
          '#type' => 'textfield',
          '#title' => $this->t("Contact Person Name"),
          '#value' =>   $text,
          '#size' => 20,
          '#required' => TRUE,
        );
    }
}