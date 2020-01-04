<?php
Namespace Drupal\nfb_washington\archive_nfb;
class representative_data extends query_base
{
    public $rep_result;

    public function get_rep_result()
    {
        return $this->rep_result;
    }
    public function create_new_meeting_options($form_state, &$options)
    {
        $this->build_state_array($form_state);
        $options = [];
        if ($form_state->getValue('select_state')) {
            foreach ($this->get_rep_result() as $rep) {
                if ($rep['meeting_id'] == '' and $form_state->getValue('select_state') == $rep['state']) {
                    $options[$rep['seminar_id']] = $rep['first_name'] . " " . $rep["last_name"] . " " . $rep['state'] . " District " . $rep['district'];
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
        $this->build_state_array($form_state); $options = [];
        if ($form_state->getValue('select_state')) {
            foreach ($this->get_rep_result() as $rep) {
                if ($rep['meeting_id'] != '' and $form_state->getValue('select_state') != '') {
                    $options[$rep['meeting_id']] = $rep['first_name'] . " " . $rep["last_name"] . " " . $rep['state'] . " District " . $rep['district'];}}
        }
    }
    public function set_home_markup($form_state, &$markup)
    {
     $this->build_state_array($form_state); $markup = "<p>".$form_state->getValue('select_state')." Representatives</p>
    <table>
    <t><th>Representative Name:</th><th>State:</th><th>District:</th><th>Meeting Status:</th><th>Meeting Time:</th><th>Meeting Location:</th><th>Contact Person:</th><th>Contact Phone:</th></tr>
    ";
     foreach ($this->get_rep_result() as $rep){
         if($form_state->getValue('select_state') != ''){
             $this->convert_meeting($rep, $meeting_status);
             $this->convert_time($rep, $meeting_time);
             $this->convert_district($rep, $district);
         $markup = $markup."<tr><th>".$rep['first_name']." ".$rep['last_name']."</th><th>".$rep['state']."</th><th>".$district."</th><th>".$meeting_status."</th><th>".$rep['meeting_date']." ".$meeting_time."</th><th>".$rep['meeting_location']."</th><th>".$rep['contact_person']."</th><th>".$rep['contact_phone']."</th></tr>";
     }}
     $markup = $markup."</table>";
     \Drupal::logger('nfb_washington')->notice($markup);
    }
    public function build_state_array($form_state)
    {
        $state = $form_state->getValue('select_state');
        unset($form_state);
        if($state == '')
        {$array = [];} else{
        $this->get_house_rep_for_state($state, $result);
        $this->find_meeting($result, $array);
        unset($result);}
        $this->rep_result = $array; unset($array);
        \drupal::logger('nfb_washington')->notice(memory_get_usage());

    }
    public function find_meeting($result, &$array)
    {
        foreach ($result as $rep)
        {
            $sem_id = $rep['seminar_id'];
            $array[$sem_id]['first_name'] = $rep['firstname'];
            $array[$sem_id]['last_name'] = $rep['lastname'];
            $array[$sem_id]['seminar_id'] = $rep['seminar_id'];
            $array[$sem_id]['district'] = $rep['district'];
            $array[$sem_id]['state'] = $rep['state'];
            $this->find_meeting_query($sem_id, $array);

        }
    }
    public function convert_meeting($rep, &$meeting_status)
    {         if($rep['meeting_id'] == "")
    {$meeting_status = "Not Scheduled";}
    else {$meeting_status = "Scheduled";}}
    public function convert_time($rep, &$meeting_time)
    {

        if($rep['meeting_time'] != ''){
            if(strlen($rep['meeting_time']) == 4){
        $hour = substr($rep['meeting_time'], 0, 2);}
            else {$hour = substr($rep['meeting_time'], 0,1);}
            if((int)$hour > 12){
        $am_pm = 'PM'; $hour = (int)$hour - 12;
                $min = substr($rep['meeting_time'],2,2);
            $meeting_time = $hour.":".$min." ".$am_pm;}
        else {$am_pm = 'AM';
        if((int)($hour) < 10)
        {
            $hour = substr($rep['meeting_time'], 0,1);
            $min = substr($rep['meeting_time'],1,2);
        }
        else { $min = substr($rep['meeting_time'],2,2);}}

        $meeting_time =  $hour.":".$min." ".$am_pm;}
        else {$meeting_time = '';}

    }
    public function convert_district($rep, &$district)
    {
        if($rep['district'] == '')
        {$district = strtoupper(substr($rep['seminar_id'], 2,9))." Senator";}
        else {$district = $rep['district'];}
    }

}