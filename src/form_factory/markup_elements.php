<?php
Namespace Drupal\nfb_washington\form_factory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\archive_nfb\representative_data;
use Drupal\nfb_washington\database\base;

class markup_elements extends date_elements
{
    public $markup;
    public $database;
    public function get_markup()
    {return $this->markup;}

    public function build_static_markup(&$form, $form_state)
    {
        $form[$this->get_element_id()] = array(
            '#type' => $this->get_element_type(),
            '#markup' => $this->get_markup(),);
    }
    public function build_ajax_markup(&$form, $form_state)
    {
        $form[$this->get_element_id()] = array(
            '#prefix' => $this->get_prefix(),
            '#type' => $this->get_element_type(),
            '#markup' => $this->get_markup(),
            '#suffix' => $this->get_suffix(),);
    }
    public function build_button_element(&$form, $form_State)
    {
        $form[$this->get_element_id()] = array(
          '#type' => $this->get_element_type(),
          '#value' => $this->t($this->get_element_title()),
          '#ajax' => array(
              'callback' => $this->get_callback(),
              'wrapper' => $this->get_wrapper(),
              'event' => 'click',),
        );
    }
    public function build_meeting_info_button(&$form, $form_state)
    {
        $this->type = 'button'; $this->title = "Find Meetings";
        $this->element_id = 'meeting_button';
        $this->callback = '::refresh_meeting'; $this->wrapper = 'meeting_markup';
        $this->event = 'click'; $this->build_button_element($form, $form_state);
    }
    public function build_meeting_info_markup(&$form, $form_state)
    {
        $this->type = "item"; $this->markup = $this->temp_ajax_test($form, $form_state);
        $this->element_id = 'meeting_info_markup';
        $this->prefix = "<div id = meeting_markup>"; $this->suffix = "</div>";
        $this->build_ajax_markup($form, $form_state);
    }
    public function temp_ajax_test(&$form, $form_state)
    {
        if($form_state->getValue('select_state') != '')
        {$this->representative_data = new representative_data();
            $this->representative_data->set_home_markup($form_state, $markup);
            $this->representative_data = null;
            return $markup;}
        else {return "<p>Please Select a state</p>";}
    }
    public function submit_button(&$form, $form_state)
    {
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),);
    }
    public function new_home_markup(FormStateInterface $form_state)
    {
        $markup = "<table>
    <tr><th class='table-header'>Member of Congress<th class='table-header'>Chamber</th><th></th></tr>";
        $this->database = new base();
        $query = "select * from nfb_Washington_members where state = '".$form_state->getValue("select_state")."'
        and active = 0 and district = 'Senate';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        $member_result = $this->database->get_result();

    }
    public function member_loop($member_result, &$member_array)
    {

    }

}