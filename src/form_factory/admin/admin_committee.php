<?php
namespace  Drupal\nfb_washington\form_factory\admin;
class admin_committee
{
    public $database;
    public function build_committee_form($committee, &$form, $form_state)
    {
        $this->initial_markup_($committee, $form, $form_state);
        $this->hidden_value($committee, $form, $form_state);
        $this->committee_name($form, $form_state);
        $this->committee_pp_id($form, $form_state);
        $this->committee_chamber($form, $form_state);
    }
    public function create_form($committee, $form, $form_State)
    {

    }
    public function edit_form($committee, $form, $form_state)
    {

    }
    public function initial_markup_($committee, &$form, $form_state)
    {
        $form['intro_markup'] = array(
          '#type' => "item",
          "#markup" => $this->markup_switch($committee),
        );
    }
    public function markup_switch($committee)
    {
        if ($committee == "add") {
            $markup = "";
        } else {
            $markup = "";
        }
        return $markup;
    }
    public function hidden_value($committee, &$form, $form_state)
    {
        $form['committee_value'] = array(
            '#type' => 'textfield',
            '#value' => $committee,
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Drupal Committee Id"
        );
    }
    public function committee_name(&$form, $form_state)
    {
        $form['committee_name'] = array(
          '#type' => 'textfield',
          '#title' => "Committee Full Name",
            '#required' => true,
            '#size' => "20",
            '#max' => 250,
        );
    }
    public function committee_pp_id(&$form, $form_state)
    {
        $form['committee_id'] = array(
            '#type' => 'textfield',
            '#title' => "Committee propublica ID",
            '#required' => true,
            '#size' => "20",
            '#max' => 250,
        );
    }
    public function committee_chamber($form, $form_state)
    {
        $form['committee_chamber'] = array(
          '#type' => "select",
          '#title' => "Chamber",
          '#options' => array(
              "senate" => "Senate",
              "joint" => "Joint",
              "house" => "House",
          ),
            "#required" => true
        );
    }


}