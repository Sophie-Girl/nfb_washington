<?php
Namespace Drupal\nfb_washington\archive_nfb;
class representative_data extends query_base
{
    public $rep_result;

    public function get_rep_result()
    {
        return $this->rep_result;
    }

    public function test_array()
    {
        $this->rep_result = array(
            '0' => array("first_name" => "Test", "last_name" => "Rep", "rep_id" => "66612",
                'district_number' => '1', 'state' => 'MD', 'meeting_id' => "", "meeting_date" => "",
                'meeting_time = "", "meeting_location' => ''),
            '1' => array("first_name" => "Some", "last_name" => "Jerk", "rep_id" => "66613",
                'district_number' => '2', 'state' => 'MD', 'meeting_id' => "1", "meeting_date" => "2/10/2020",
                'meeting_time = "4:30pm", "meeting_location' => 'Office Building'),
            '2' => array("first_name" => "Another", "last_name" => "Jerk", "rep_id" => "66614",
                'district_number' => '1', "state" => "CA", 'meeting_id' => "2", "meeting_date" => "2/10/2020",
                'meeting_time = "6:00pm", "meeting_location' => 'Office Building'),
            '3' => array('first_name' => 'Joe', "last_name" => "Smith", 'rep_id' => '66615',
                'district_number' => '3', 'state' => 'MD', 'meeting_id' => "", "meeting_date" => "",
                'meeting_time = "", "meeting_location' => ''),
            '4' => array('first_name' => 'Jane', "last_name" => 'Dane', "rep_id" => '66616',
                'district_number' => '4', 'state' => 'MD', 'meeting_id' => "", "meeting_date" => "",
                'meeting_time = "", "meeting_location' => ''),
            '5' => array('first_name' => 'Sophie', "last_name" => 'Test', "rep_id" => '66617',
                'district_number' => '5', 'state' => 'MD', 'meeting_id' => "3",
                "meeting_date" => "2/10/2020",  'meeting_time = "5:30pm", "meeting_location' => 'Office Building'),
        );
    }

    public function create_new_meeting_options($form_state, &$options)
    {
        $this->test_array();
        $options = [];
        if ($form_state->getValue('select_state')) {
            foreach ($this->get_rep_result() as $rep) {
                if ($rep['meeting_id'] == '' and $form_state->getValue('select_state') == $rep['state']) {
                    $options[$rep['rep_id']] = $rep['first_name'] . " " . $rep["last_name"] . " " . $rep['state'] . " District " . $rep['district_number'];
                }
            }
        }
    }

    public function new_meeting_options_element($form_state, &$options)
    {
        $this->create_new_meeting_options($form_state, $options);
        if ($form_state->getValue('select_state')) {
            \drupal::logger('nfb_washington')->notice(print_r($options, true));
        }
    }

    public function create_update_meeting_options($form_state, &$options)
    {
        $this->test_array(); $options = [];
        if ($form_state->getValue('select_state')) {
            foreach ($this->get_rep_result() as $rep) {
                if ($rep['meeting_id'] != '' and $form_state->getValue('select_state') == $rep['state']) {
                    $options[$rep['rep_id']] = $rep['first_name'] . " " . $rep["last_name"] . " " . $rep['state'] . " District " . $rep['district_number'];}}}
    }
    public function set_home_markup($form_state, &$markup)
    {
     $this->test_array(); $markup = "<p>".$form_state->getValue('select_state')."Representatives</p>
    <table>
    <t><th>Representative Name:</th><th>State:</th><th>District:</th><th>Meeting Status:</th><th>Meeting Time:</th><th>Meeting Location:</th></tr>
    ";
     foreach ($this->get_rep_result() as $rep)
     {
         if($rep['state'] == $form_state->getValue('select_state')){
         if($rep['meeting_id'] == "")
         {$meeting_status = "Not Scheduled";}
         else {$meeting_status = "Scheduled";}
         $markup = $markup."<tr><th>".$rep['first_name']." ".$rep['last_name']."</th><th>".$rep['state']."</th><th>".$rep['district_number']."</th><th>".$meeting_status."</th><th>".$rep['Meeting_date']." ".$rep['meeting_time']."</th><th>".$rep['meeting_location']."</th></tr>";
     }}
     $markup = $markup."</table>";
    }

}