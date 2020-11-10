<?php
namespace  Drupal\nfb_washington\form_factory\admin;
class admin_issue_home
{
    public $database;
    public function Build_issue_home_form(&$form, $form_state)
    {

    }
    public function initial_markup(&$form, $form_state)
    {
        $year = date("Y");
        $markup = "<p>Issues that are currently being brought up during the ".$year." Washington
    Seminar are listed below. Issue year is assigned by calender date. If you wish to created issues for 
    the next Washington Seminar. Please wait until that calender year. Each year there is a maximum of three
     issues that can be created. To create an issue  click the create issue button below</p>
     <p><a href='/nfb_washington/admin/issue/create' class = 'button-1'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creave Issue&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>";
        return $markup;
    }
    public function issue_table(&$form, $form_state)
    {

    }


}