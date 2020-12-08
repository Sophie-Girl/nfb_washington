<?php
namespace Drupal\nfb_washington\form_factory;

class textfield_elements extends element_base
{
    public $database;
    public $min; // minimum input
    public $max; // maximum input
    public $size; // size of textfield
    public $issue_1;
    public $issue_2;
    public $issue_3;
    public function get_element_min()
    {return $this->min;}
    public function get_element_max()
    {return $this->max;}
    public function get_element_size()
    {return $this->size;}
    public function  get_issue1()
    {return $this->issue_1;}
    public function  get_issue2()
    {return $this->issue_2;}
    public function  get_issue3()
    {return $this->issue_3;}
    public function build_static_textfield(&$form, $form_state) // build all textfield elements that require no conditionals
    {   $form[$this->get_element_id()] = array(
            '#type' =>  $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()),
            '#min' => $this->get_element_min(),
            '#max' => $this->get_element_max(),
            '#size' => $this->get_element_size(),
            '#required' => $this->get_element_required_status(),);}
    public function update_first_name(&$form, $form_state)
    {

        $form['nfb_contact_name'] = array(
            '#type' => 'textfield',
            '#title' => "Contact Person Name",
            '#size' => 20,
            '#required' => TRUE,
        );

    }
    public function build_prefix_textfield(&$form, $form_state)
    {
        $form[$this->get_element_id()] = array(
            '#prefix' => $this->get_prefix(),
            '#type' =>  $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()),
            '#min' => $this->get_element_min(),
            '#max' => $this->get_element_max(),
            '#size' => $this->get_element_size(),
            '#required' => $this->get_element_required_status(),);
    }
    public function build_suffix_textfield(&$form, $form_state)
    {
        $form[$this->get_element_id()] = array(
            '#suffix' => $this->get_suffix(),
            '#type' =>  $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()),
            '#min' => $this->get_element_min(),
            '#max' => $this->get_element_max(),
            '#size' => $this->get_element_size(),
            '#required' => $this->get_element_required_status(),);
    }
    public function contact_first_name_element(&$form, $form_state)
    {   $this->type = 'textfield'; $this->title = "Point of Contact First Name";
        $this->min = '1'; $this->max = '30';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_f_name_1';
        $this->prefix = "<div id='data_wrapper'>";
        $this->build_prefix_textfield($form, $form_state);}
    public function contact_last_name_element(&$form, $form_state)
    {   $this->type = 'textfield'; $this->title = "Point of Contact Last Name";
        $this->min = '1'; $this->max = '30';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_l_name_1';
        $this->build_static_textfield($form, $form_state);}
    public function contact_email_element(&$form, $form_state) // MOC contact info. todo ask Ross if we need a phone element
    {   $this->type = 'textfield'; $this->title = "Point of Contact Phone";
        $this->min = '1'; $this->max = '40';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_phone_1';
        $this->build_static_textfield($form, $form_state);}
    public function MOC_staff_name_element(&$form, $form_state)
    {
        $this->type = 'textfield'; $this->title = "Member of Congress' Staff Name";
        $this->min = '1'; $this->max = '60';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'moc_staff_name';
        $this->build_static_textfield($form, $form_state);}
    public function comment_element_data_set(&$form, $form_state) // builds the comment fields. Needs to have id and title set before use.
    {   $this->type = 'textarea';
        $this->min = '1'; $this->max = '500';
        $this->required = false; $this->size = '240';
        $this->build_static_textfield($form, $form_state);}
    public function meeting_comment_element(&$form, $form_state)
    {   $this->element_id = 'meeting_comment'; $this->title = 'Meeting Notes';
        $this->comment_element_data_set($form, $form_state);}
    public function issue_1_comment_element(&$form, $form_state)
    {   $this->element_id = 'issue_1_comment';
        $this->get_issue_names();
        $this->title = 'Comments on Reception to '.$this->get_issue1();
        $this->comment_element_data_set($form, $form_state);}
    public function issue_2_comment_element(&$form, $form_state)
    {   $this->element_id = 'issue_2_comment';
        $this->get_issue_names();
        $this->title = 'Comments on Reception to '.$this->get_issue2();
        $this->comment_element_data_set($form, $form_state);}
    public function issue_3_comment_element(&$form, $form_state)
    {   $this->element_id = 'issue_3_comment';
        $this->get_issue_names();
        $this->title = 'Comments on Reception to '.$this->get_issue3();
        $this->comment_element_data_set($form, $form_state);}
     public function meeting_comments_element(&$form, $form_state)
     {  $this->element_id = "meeting_location"; $this->type = 'textfield';
        $this->title = "Zoom Meeting Information"; $this->size = '20';
        $this->min = '0'; $this->max = '200'; $this->required = TRUE;
        $this->suffix = "</div>";
         $this->build_suffix_textfield($form, $form_state);
     }
     public function MOC_contact_element(&$form, $form_State)
     {
         $this->element_id = 'moc_contact'; $this->title = "Member of Congress Contact";
         $this->size = '20'; $this->min = '0'; $this->max = '200';
         $this->required = false; $this->type = 'textfield';
         $this->build_static_textfield($form, $form_State);
     }
     public function get_issue_names()
     {
         //
         $this->database = new base(); $year = date("Y");
         $query = "select * from  nfb_washington_issues where issue_year = '".$year."'
         order by issue_id ascending";
         $key = 'issue_id';
         $this->database->select_query($query, $key);
         $count = 1;
         foreach ($this->database->get_result() as $issue)
         {
             $issue = get_object_vars($issue);
             switch ($count)
             {
                 case 1:
                     $this->issue_1 = $issue['name'];
                     break;
                 case 2:
                     $this->issue_2 = $issue['name'];
                     break;
                 case 3:
                     $this->issue_3 = $issue['name'];
                     break;
             }
         }

     }
}