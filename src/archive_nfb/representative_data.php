<?php
Namespace Drupal\nfb_washington\archive_nfb;
class representative_data extends query_base
{
    public $rep_result;
    public function get_rep_result()
    {return $this->rep_result;}
    public function test_array()
    {
        $this->rep_result = array(
            '0' => array("first_name" => "Test", "last_name" => "Rep", "rep_id" => "66612",
                'district_number' => '1', 'state' => 'MD', 'meeting_id' => ""),
            '1' => array("first_name" => "Some", "last_name" => "Jerk", "rep_id" => "66613",
                'district_number' => '2', 'state' => 'MD' ,'meeting_id' => "1"),
            '2' => array("first_name" => "Another", "last_name" => "Jerk", "rep_id" =>"66614",
                'district_number' => '1', "state" => "CA", 'meeting_id' => "2"),
        );
    }
    public function create_new_meeting_options($form_state, &$options)
    {
        $this->test_array(); $options[''] = "- Select -";
        foreach($this->get_rep_result() as $rep)
        {
            if($rep['meeting_id'] == '' and $form_state->getValue('select_state') == $rep['state'])
            {
                $options[$rep['rep_id']] = $rep['first_name']." ".$rep["last_name"]." ".$rep['state']." District ".$rep['district_number'];
            }
        }
    }
    public function new_meeting_options_element($form_state, &$options)
    {
        if($form_state->getValue('select_state'))
        {$options[''] = ['Select a State'];
        \drupal::logger('nfb_washington')->notice("if you haven't selected a sate. We got a problem");}
        else {$this->create_new_meeting_options($form_state, $options);}
    }
}