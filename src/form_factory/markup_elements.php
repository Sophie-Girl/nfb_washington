<?php
Namespace Drupal\nfb_washington\form_factory;
class markup_elements extends date_elements
{
    public $markup;
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
        $this->type = "item"; $this->markup = '<p>This is just a test</p>';
        $this->element_id = 'meeting_info_markup';
        $this->prefix = "<div id = meeting_markup>"; $this->suffix = "</div>";
        $this->build_ajax_markup($form, $form_state);
    }
}