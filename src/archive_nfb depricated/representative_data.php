<?php
Namespace Drupal\nfb_washington\archive_nfb;
use Drupal\Core\Form\FormStateInterface;

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
    public function create_new_rating_options($form_state, &$options)
    {
        $this->build_state_array($form_state);
        $options = [];
        if ($form_state->getValue('select_state')) {
            foreach ($this->get_rep_result() as $rep) {
                \drupal::logger("nfb_washington")->notice(print_r($rep, 1));
                if ( $form_state->getValue('select_state') == $rep['state'] && $rep['issue1'] == "no rating"
                && $rep['issue2'] == "no rating"    && $rep['issue3'] == "no rating") {

                    $options[$rep['seminar_id']] = $rep['first_name'] . " " . $rep["last_name"] . " " . $rep['state'] . " District " . $rep['district'];
                }
            }
        }
    }
    public function  create_update_rating_options($form_state, &$options)
    {
        $this->build_state_array($form_state);
        $options = [];
        if ($form_state->getValue('select_state')) {
            foreach ($this->get_rep_result() as $rep) {
                if ( $form_state->getValue('select_state') == $rep['state'] && $rep['issue1'] != "no rating"
            ||   $form_state->getValue('select_state') == $rep['state'] && $rep['issue2'] != "no rating" ||
                    $form_state->getValue('select_state') == $rep['state']    && $rep['issue3'] != "no rating") {
                    $options[$rep['seminar_id']] = $rep['first_name'] . " " . $rep["last_name"] . " " . $rep['state'] . " District " . $rep['district'];
                }
            }
        }
    }

    public function new_meeting_options_element($form_state, &$options)
    {
        $this->create_new_meeting_options($form_state, $options);
        if ($form_state->getValue('select_state')) {
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
    <tr><th>Representative Name:</th><th>State:</th><th>District:</th><th>Meeting Status:</th><th>Meeting Time:</th><th>Meeting Location:</th><th>Attending In Person</th><th>Member of Congress Contact</th><th>Contact Person:</th><th>Contact Phone:</th></tr>
    ";
     foreach ($this->get_rep_result() as $rep){
         if($form_state->getValue('select_state') != ''){
             $this->convert_meeting($rep, $meeting_status);
             $this->convert_time($rep, $meeting_time);
             $this->convert_district($rep, $district);
             $this->meeting_location($rep, $meeting_location);
             $this->yes_no_conversion($rep, $expected);
             $this->member_of_congress_contact($rep);
             $this->convert_contact_info($rep, $contact_person,$contact_phone);
         $markup = $markup."<tr><td>".$rep['first_name']." ".$rep['last_name']."</td><td>".$rep['state']."</td><td>".$district."</td><td>".$meeting_status."</td><td>".$rep['meeting_date']." ".$meeting_time."</td><td>".$meeting_location."</td><td>".$expected."</td><td>".$rep['moc_contact']."</td><td>".$contact_person."</td><td>".$contact_phone."</td></tr>";
     }}
     $markup = $markup."</table>";
    }
    public function meeting_location($rep, &$meeting_location)
    {
        if ($rep['meeting_location'] == "")
        {
            $meeting_location = "N/A";
        }
        else $meeting_location = $rep['meeting_location'];
    }
    public function member_of_congress_contact(&$rep)
    {
        if($rep['moc_contact'] == "")
        {$rep['moc_contact'] = "N/A";}
    }
    public function yes_no_conversion($rep, &$expected)
    {
        if($rep['rep_expected'] != '')
        {
            if($rep['rep_expected'] == '1')
            {$expected = "Yes";}else {$expected = "No";}
        } else{$expected = "N/A";}
    }
    public function convert_contact_info($rep, &$contact_person, &$contact_phone)
    {
        if($rep['contact_person'] == "")
        {$contact_person = "N/A";
        $contact_phone = "N/A";}
        else {$contact_person = $rep['contact_person']; $contact_phone = $rep['contact_phone'];}
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
            if((int)$hour > 11){
        $am_pm = 'PM';
        if((int)$hour != '12'){$hour = (int)$hour - 12;}
                $min = substr($rep['meeting_time'],2,2);
            $meeting_time = $hour.":".$min." ".$am_pm;}
        else {$am_pm = 'AM';
        if((int)($hour) < 10)
        {
            $hour = substr($rep['meeting_time'], 0,1);
            if((int)$hour == 0){$hour = 12;}
            $min = substr($rep['meeting_time'],1,2);
        }
        else { $min = substr($rep['meeting_time'],2,2);}}

        $meeting_time =  $hour.":".$min." ".$am_pm;}
        else {$meeting_time = 'N/A';}

    }
    public function convert_district($rep, &$district)
    {
        if($rep['district'] == '')
        {$district = strtoupper(substr($rep['seminar_id'], 2,9))." Senator";}
        else {$district = $rep['district'];}
    }
    public function get_rep_data_update(FormStateInterface $form_state, $category, &$text)
    {
        if($form_state->getValue('select_rep') != '')
        {
            $this->establish_connection(); $test = $this->sql_connection->query("
            select ".$category." from  aaxmarwash_activities where activity_id = '".$form_state->getValue('select_rep')."'");
            if($test) { $result = $test->fetch_all(MYSQLI_ASSOC);
            $text = $result['0'][$category];} else {$text = "Error";}
        } else $text ="";
    }
    public function build_issue_array($form_state)
    {
        $state = $form_state->getValue('select_state');
        unset($form_state);
        if ($state = "")
        {$array = [];}


    }


}