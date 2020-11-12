<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\nfb_washington\database\base;

class admin_issue_home
{
    public $database;
    public function Build_issue_home_form(&$form, $form_state)
    {
        $this->initial_element($form, $form_state);
        $this->issue_table($form, $form_state);
    }
    public function initial_markup()
    {
        $year = date("Y");
        $markup = "<p>Issues that are currently being brought up during the ".$year." Washington
    Seminar are listed below. Issue year is assigned by calender date. If you wish to created issues for 
    the next Washington Seminar. Please wait until that calender year. Each year there is a maximum of three
     issues that can be created. To create an issue  click the create issue button below. All issues for the past 4 years are listed below. 
     you can edit them with their corresponding edit button</p>
     <p><a href='/nfb_washington/admin/issue/create' class = 'button-1' role = 'button'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Create Issue&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>";
        return $markup;
    }
    public function initial_element(&$form, $form_state)
    {
        $form['header_markup'] = array(
            '#type' => 'item',
            '#markup' => $this->initial_markup()
        );
    }
    public function issue_table(&$form, $form_state)
    {
        $this->table_query($result);
        $this->create_table($markup, $result);
        $form['issue_table'] = array(
          '#type' => "item",
          '#markup' => $markup,
        );
    }
    public function table_query(&$result)
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues order by issue_year desc limit 12;";
        $key = 'issue_id';
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
    }
    public function create_table(&$markup, $result)
    {
        $count = 1;
        $markup = "<table width='100%'>
<t><th class='table-header'>Issue Name</th><th class='table-header'>Bill Slug</th><th class='table-header'>First Use</th><th class='table-header'>Year</th><th class='table-header'>Actions</th></tr>";
        foreach($result as $issue)
        {
            $issue = get_object_vars($issue);
            if($issue['primary_status'] == "0")
            {$primary = "Yes";} else {$primary = "No";}
            $markup = $markup."<tr><td>".$issue['issue_name']."</td><td>".$issue['bill_slug']."</td><td>".$primary."</td><td>".$issue['issue_year']."</td><td><a href='/nfb_washington/admin/issue/".$issue['issue_id']."' class='button-".$count."' role='button'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edit/View&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> </td></tr>";
            $this->count_catch($count);
        }
        $markup = $markup."</table>";
    }
    public function count_catch(&$count)
    {
        if($count == 1)
        {
            $count = 2;
        }
        elseif($count == 2)
        {
            $count = 3;
        }
        else{ $count = 1;}
    }


}