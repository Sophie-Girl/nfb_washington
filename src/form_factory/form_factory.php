<?php
Namespace Drupal\nfb_washington\form_factory;
class form_factory extends update_form_ajax_test
{
    public function build_new_meeting_time(&$form, $form_state, $meeting)
    {
        if($meeting == "new"){
        $this->state_ajax_select_element($form,  $form_state);
        $this->state_rep_ajax_select_element($form, $form_state);}
        $this->meeting_hidden_value($form, $form_state, $meeting);
        if($meeting == "new") {
        $this->contact_first_name_element($form, $form_state);
        $this->contact_last_name_element($form, $form_state);}
        else { $this->update_first_name($form, $form_state);}
        $this->contact_email_element($form, $form_state);
        $this->meeting_day_element($form, $form_state);
        $this->meeting_time_element($form, $form_state);
        $this->meeting_comments_element($form, $form_state);
        $this->new_attendance_element($form, $form_state);
        $this->MOC_contact_element($form, $form_state);
        if($meeting == "new"){
        $this->submit_button($form, $form_state);}
        else{
            $this->build_confirmation_checkbox($form, $form_state);
            $this->conditional_submit($form, $form_state);
        }
    }
    public function build_update_rating_form(&$form, $form_state, $rating)
    {
        if($rating == "new"){
            $this->state_ajax_select_element($form,  $form_state);
            $this->state_rep_ajax_select_element($form, $form_state);
        }
        $this->raiting_hidden_value($form, $form_state, $rating);
        $this->update_first_name($form, $form_state);
        $this->contact_email_element($form, $form_state);
        $this->issue_1_ranking_element($form, $form_state);
        $this->issue_1_comment_element($form, $form_state);
        $this->issue_2_ranking_element($form, $form_state);
        $this->issue_2_comment_element($form, $form_state);
        $this->issue_3_ranking_element($form, $form_state);
        $this->issue_3_comment_element($form, $form_state);
        if($rating == 'new')
        {$this->submit_button($form, $form_state);}
        else{
            $this->build_confirmation_checkbox($form, $form_state);
            $this->conditional_submit($form, $form_state);
        }
    }
    public function build_home_page_form(&$form, $form_state){
        $this->state_select_element($form, $form_state);
        $this->build_meeting_info_button($form, $form_state);
        $this->build_meeting_info_markup($form, $form_state);
    }
    public function build_confirmation_checkbox(&$form, $form_state)
    {
        $form['confirm'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('I confirm I want to make this update'),
            '#required' => 'true',
        );
    }
    public function conditional_submit(&$form, $form_state)
    {
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#states' => [
                'visible' =>[
                    [':input[name="confirm"]' => ['checked' => true]]],],
        );
    }
    public function meeting_hidden_value(&$form, $form_state, $meeting)
    {
        $form['meeting_value'] = array(
            '#type' => 'textfield',
            '#value' => $meeting,
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Drupal meeting Id"
        );
    }
    public function raiting_hidden_value(&$form, $form_state, $rating)
    {
        $form['rating_value'] = array(
            '#type' => 'textfield',
            '#value' => $rating,
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Drupal meeting Id"
        );
    }

}