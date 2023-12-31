<?php
namespace Drupal\nfb_washington\form_factory;
use Drupal\nfb_washington\database\base;

class textfield_elements extends element_base
{
    public $database;
    public $min; // minimum input
    public $max; // maximum input
    public $size; // size of textfield
    public $issue_1;
    public $issue_2;
    public $issue_3;
    public $issue_4;
    public $issue_5;
    public $issue_count; // limit to the number of issues
    public $virtual_in_person;
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
    public function get_issue_4()
    {return $this->issue_4;}
    public function get_issue_5()
    {return $this->issue_5;}
    public function get_issue_count()
    {return $this->issue_count;}
    public function get_virtual_or_in_person_text()
    {return $this->virtual_in_person;}
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
            '#title' => "NFB Contact Person Name",
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
    {   $this->type = 'textfield'; $this->title = "NFB Contact First Name";
        $this->min = '1'; $this->max = '30';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_f_name_1';

        $this->build_static_textfield($form, $form_state);
        }
    public function contact_last_name_element(&$form, $form_state)
    {   $this->type = 'textfield'; $this->title = "NFB Contact Last Name";
        $this->min = '1'; $this->max = '30';
        $this->size = '20'; $this->required = TRUE;
        $this->element_id = 'nfb_civicrm_l_name_1';
        $this->build_static_textfield($form, $form_state);}
    public function contact_email_element(&$form, $form_state) // MOC contact info. todo ask Ross if we need a phone element
    {   $this->type = 'textfield'; $this->title = "NFB Contact Phone";
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
    public function issue_4_comment_element(&$form, $form_state)
    {
        $this->element_id = 'issue_4_comment';
        $this->get_issue_names();
        $this->title = 'Comments on Reception to '.$this->get_issue_4();
        $this->comment_element_data_set($form, $form_state);
    }
    public function issue_5_comment_element(&$form, $form_state)
    {
        $this->element_id = 'issue_5_comment';
        $this->get_issue_names();
        $this->title = 'Comments on Reception to '.$this->get_issue_5();
        $this->comment_element_data_set($form, $form_state);
    }

    public function meeting_comments_element(&$form, $form_state)
     {   $this->set_up_in_person_virtual();
         $this->element_id = "meeting_location"; $this->type = 'textfield';
        $this->title = $this->get_virtual_or_in_person_text(); $this->size = '20';
        $this->min = '0'; $this->max = '200'; $this->required = TRUE;
         $this->build_static_textfield($form, $form_state);
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
         order by issue_id ASC";
         $key = 'issue_id';
         $this->database->select_query($query, $key);
         $count = 1;
         foreach ($this->database->get_result() as $issue)
         {
             $issue = get_object_vars($issue);
             switch ($count)
             {
                 case 1:
                     $this->issue_1 = $issue['issue_name'];
                     break;
                 case 2:
                     $this->issue_2 = $issue['issue_name'];
                     break;
                 case 3:
                     $this->issue_3 = $issue['issue_name'];
                     break;
                 case 4:
                     $this->issue_4 = $issue['issue_name']; break;
                 case 5:
                     $this->issue_5 = $issue['issue_name']; break;
             }
             $count++;
         }

     }
     public function set_up_in_person_virtual(){
    $this->database = new base();
    $query = "select * from nfb_washington_config where setting = 'seminar_type';";
    $key = 'value';
    $this->database->select_query($query, $key);
    $type = null;
    foreach($this->database->get_result() as$types)
         {
             $types = get_object_vars($types);
             if($type == null)
             {$type = $types['value'];}
         }
    if($type == "virtual"){$this->virtual_in_person = "Zoom Meeting Id";}
    else {$this->virtual_in_person = "Meeting Location:";}
    }
}