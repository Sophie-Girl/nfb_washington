<?php
namespace Drupal\nfb_washington\form_factory;
class textfield_elements extends base
{
    public $min; // minimum input
    public $max; // maximum input
    public $size; // size of textfield
    public function get_element_min()
    {return $this->min;}
    public function get_element_max()
    {return $this->max;}
    public function get_element_size()
    {return $this->size;}
    public function build_static_textfield(&$form) // build all textfield elements that require no conditionals
    {   $form[$this->get_element_id()] = array(
            '#type' =>  $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()),
            '#min' => $this->get_element_min(),
            '#max' => $this->get_element_max(),
            '#size' => $this->get_element_size(),
            '#required' => $this->get_element_required_status(),);}
    public function contact_first_name_element(&$form)
    {   $this->type = 'textfield'; $this->title = "Point of Contact First Name";
        $this->min = '1'; $this->max = '30';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_f_name_1';
        $this->build_static_textfield($form);}
    public function contact_last_name_element(&$form)
    {   $this->type = 'textfield'; $this->title = "Point of Contact Last Name";
        $this->min = '1'; $this->max = '30';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_l_name_1';
        $this->build_static_textfield($form);}
    public function contact_email_element(&$form) // MOC contact info. todo ask Ross if we need a phone element
    {   $this->type = 'textfield'; $this->title = "Point of Contact Email";
        $this->min = '1'; $this->max = '40';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_email_1';
        $this->build_static_textfield($form);}
    public function MOC_staff_name_element(&$form)
    {
        $this->type = 'textfield'; $this->title = "Member of Congress' Staff Name";
        $this->min = '1'; $this->max = '60';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'moc_staff_name';
        $this->build_static_textfield($form);}
    public function comment_element_data_set(&$form) // builds the comment fields. Needs to have id and title set before use.
    {   $this->type = 'textarea';
        $this->min = '1'; $this->max = '500';
        $this->required = TRUE; $this->size = '240';
        $this->build_static_textfield($form);}
    public function meeting_comment_element(&$form)
    {   $this->element_id = 'meeting_comment'; $this->title = 'Meeting Notes';
        $this->comment_element_data_set($form);}
    public function issue_1_comment_element(&$form)
    {   $this->element_id = 'issue_1_comment';
        $this->title = 'Comments on Reception to Issue 1';
        $this->comment_element_data_set($form);}
    public function issue_2_comment_element(&$form)
    {   $this->element_id = 'issue_2_comment';
        $this->title = 'Comments on Reception to Issue 2';
        $this->comment_element_data_set($form);}
    public function issue_3_comment_element(&$form)
    {   $this->element_id = 'issue_3_comment';
        $this->title = 'Comments on Reception to Issue 3';
        $this->comment_element_data_set($form);}
}