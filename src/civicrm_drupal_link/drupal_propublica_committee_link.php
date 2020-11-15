<?php
Namespace Drupal\nfb_washington\civicrm_drupal_link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\propublica\committee;

class drupal_propublica_committee_link
{
    public $propublica;
    public  $committee_name;
    public function get_committee_name()
    {return $this->committee_name;}
    public function __construct(committee $committee)
    {
        $this->propublica = $committee;
    }
    public function set_up_initial_query_add_committee(FormStateInterface $form_state)
    {
        $this->committee_name = $form_state->getValue("committee_name");
        $this->propublica->entity = "committees";
        $this->establish_propublica_dependencies();
        $this->propublica->search_criteria_1 = $form_state->getValue("committee_chamber");
        $this->propublica->general_committee_search();
    }
    public function establish_propublica_dependencies()
    {
        $this->propublica->set_api_key(); $this->propublica->set_congress_number();
    }
    public function set_up_specific_query_for_edits_and_members(FormStateInterface $form_state)
    {
        $this->propublica->entity = "committees";
        $this->establish_propublica_dependencies();
    }
    public function database_value_lookup()
    {

    }

}