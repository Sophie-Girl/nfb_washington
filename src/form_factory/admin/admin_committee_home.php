<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\nfb_washington\database\base;

class admin_committee_home
{
    public $database;
    public function build_com_home_form(&$form, $form_state)
    {
        $this->initial_markup($form, $form_state);
        $this->table_element($form, $form_state);
    }
    public function initial_markup(&$form, $form_state)
    {
        $form['initial_markup'] = array(
          '#type' => "item",
          "#markup" => "<p>Congressional Committees that have been entered into the system are listed below.
To add a new issues click the corresponding button below. Each Committee cna be edited, have maintenance performed
on its members (remove those who are not currently in the committee, and in new additions), and have the committee 
linked to an issue
<br><a href='/nfb_washington/admin/committee/create' role='button' class='button-1'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add Committee&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>"
        );
    }
    public function select_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee";
        $key = "committee_id";
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->database = null;
        return $result;
    }
    public function table_element(&$form, $form_state)
    {
        $form['committee_table'] = array(
            '#type' => "item",
            '#markup' => $this->table_markup()
        );
    }
    public function table_markup()
    {
        $sql_result = $this->select_query();
        $count = 0;
        $markup = "<table>
<tr><th class='table-header'>Committee Name</th><th class='table-header'>Propublica ID</th><th class='table-header'> Actions</th></tr>";
        foreach ($sql_result as $committee)
        {
            $committee = get_object_vars($committee);
            $markup = $markup."<tr><td>".$committee['committee_name']."</td><td>".$committee['h']."</td></tr>";
            $count++;
        }
        $markup = $markup."</tabe>";
        if($count == 0)
        {$markup = "<p>No committees have been added</p>";}
        return $markup;
    }
}