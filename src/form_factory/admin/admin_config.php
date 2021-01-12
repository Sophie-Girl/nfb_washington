<?php
namespace  Drupal\nfb_washington\form_factory\admin;
class admin_config
{
    public function  API_key_markup(&$form, $form_state)
    {
        $form['explain_markup'] = array(
          "#type" => "item",
          "#markup" => "<p>The API Key is what allows us to make requests to Propublica's API for congressional data. 
For information on th eAPi please visit their website <a href='https://projects.propublica.org/api-docs/congress-api/'>here.</a></p>"
        );
    }
    public function APIKey_text_Field(&$form, $form_state)
    {
        $form['pp_api_key'] = array(
          '#type' => 'textfield',
           '#size' => '20',
           '#title' => "Propublica API Key (Required to pull Congressional data)",
           '#required' => true,
            '#max' => '50',
        );
    }
    public function Congress_Number(&$form, $form_state)
    {
        $form['congress_number'] = array(
          '#type' => 'select',
          '#title' => "Congress Number (i.e 116th Congress)",
          "#options" => array("115" => "115th",
                              "116" => "116th",
              "117" => "117th",
              "118" => "118th",
              '119' => "119th",
              "120" => "120th"
              ),
          "#required" => "true",
        );
    }
    public function seminar_type(&$form, $form_state)
    {
        $form['seminar'] = array(
            '#type' => 'select',
            '#title' => "Is Washington Seminar In Person or Virtual",
            "#options" => array(
                "in_person" => "In Person",
                "virtual" => "Virtual",
            ),
            "#required" => "true",
        );
    }
    public function  issue_number(&$form, $form_state)
    {
        $form['issue_number'] = array(
            '#type' => 'select',
            '#title' => "Issue Number for This Year",
            "#options" => array(
               "1" => "1",
                "2" => "2",
                "original_spec" => "3",
                "death" => "4",
                "just_in_case" => "5"
            ),
            "#required" => "true",
        );
    }

    public function email_address(&$form, $form_state)
    {
        $form['staff_email'] = array(
            '#type' => 'textfield',
            '#title' => "Staff Email Address",
            '#size' => '20',
            '#min' => '6',
            "#required" => "true",
        );
    }
    public function build_form_array(&$form, $form_state)
    {
        $this->API_key_markup($form, $form_state);
        $this->APIKey_text_Field($form, $form_state);
        $this->Congress_Number($form, $form_state);
        $this->issue_number($form, $form_state);
        $this->seminar_type($form, $form_state);
        $this->email_address($form, $form_state);
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",);
    }

}