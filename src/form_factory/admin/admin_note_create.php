<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\Core\Form\FormStateInterface;

class admin_note_create
{
    public function build_form_Array(&$form, FormStateInterface $form_state, $note)
    {
        $this->hidden_note_value($form, $form_state, $note);
        $this->note_year($form, $form_state);
        $this->note_type_element($form, $form_state);
        $this->note_text($form, $form_state);
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",
        );
    }
    public function note_type_element(&$form, $form_state)
    {
        $form['note_type'] = array(
          '#type' => 'select',
          '#title' => "Select Note Type",
            '#required' => true,
            '#options' => array(
                "bill_sponsor" => "Bill Sponsor",
                "bill_co_sponsor" => "Bill Co-Sponsor",
                "state_convention" => "Spoke at a State Convention",
                "national_convention" => " Spoke at National convention")
        );
    }
    public function note_text(&$form, $form_state)
    {
        $form['note_text'] =- array(
          "#type" => "textarea",
          "#title" => "Note Text",
          "#required" => true,
          "#max" => 500,
          "#min" => 1,
        );
    }
    public function hidden_note_value(&$form, $form_state, $note)
    {
        $form['note_value'] = array(
            '#type' => 'textfield',
            '#value' => $note,
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Issue Id"
        );
    }
    public function note_year(&$form, $form_state)
    {
        $form['note_year'] = array(
            '#type' => 'textfield',
            '#etitle' => "Year",
            '#max' => 5,
            '#required' => true
        );
    }

}