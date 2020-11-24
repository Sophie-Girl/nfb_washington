<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;

class admin_note_home
{
    public $database;
    public function build_form_array($form, FormStateInterface $form_state)
    {
        $this->year_select($form, $form_state);
        $this->note_markup($form, $form_state);
    }
    public function year_select($form, $form_state)
    {
        $form['year_select'] = array(
           '#type' => 'select',
           '#title' => "select Year",
           '#required' => true,
           '#options' => array(
               '2020' => '2020',
               '2021' => '2021',
               '2022' => '2022',
               '2023' => '2023',
               '2024' => '2024',
               '2025' => '2025',
               '2026' => '2026',
               '2027' => '2027',
               '2028' => '2028',
               '2029' => '2029',
               '2030' => '2030'
           ) ,
            "#ajax" => array(
                "event" => "change",
                "callback" => '::markup_refresh',
                'wrapper' => "note_markup"
            ),
        );
    }
    public function note_markup($form, FormStateInterface $form_state)
    {
        $form['note_table'] = array(
          '#prefix' => "<div id = 'note_markup'>",
            '#type' => 'item',
          '#markup' => $this->build_table($form_state),
            '#suffix' => '</div>'
        );
    }
    public function build_table(FormStateInterface $form_state)
    {
        if($form_state->getValue('year_select') != '')
        {
            $year = $form_state->getValue("year_select");
            $markup = $this->table_query($year);
        }
        else { $markup = "<p>Please select a year</p>";}
        return $markup;
    }
    public function table_query($year)
    {
        $this->database = new base();
        $query = "select * from nfb_washington_note where note_year = '".$year."';";
        $key = 'note_id';
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->database = null;
        $this->table_builder($result, $markup);
        return $markup;
    }
    public function table_builder($result, &$markup)
    {
        $markup =  "<p>Bellow are all issues for the selected year</p>
        <table>
        <tr><th class='table-header'>Note Type</th><th>Note Text</th><th>Note Year</th><th>Created User</th><th>Last Modified by</th><th>Actions</th></tr>";
        foreach($result as $note)
        {
            $note = get_object_vars($note);
            $markup = $markup."<tr><td>".$note['note_type']."</td><td>".$note['note_text']."</td><td>".$note['note_year']."</td><td>".$note['created_user']."</td><td>".$note['last_modified_user']."</td><td> <a href='/nfb_washington/admin/note/".$note['note_id']."' class='button-1' role='button'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edit&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;<a href='/nfb_washington/admin/issue_link' class='button-2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Link to Member&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></td></tr>";
        }
         $markup= $markup. "</table>";
    }
}