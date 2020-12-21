<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\civicrm\Civicrm;
use Drupal\nfb_washington\civicrm\civi_query;

class meeting_report
{
    public $database;
    public $civicrm;
    public function build_form(&$form, $form_state)
    {

    }
    public function state_select()
    {
        $form['state_select'] = array(
          '#type' => 'select',
          '#title' => 'Select State',
          '#options' => $this->state_options(),
          '#required' => false,
        );
    }
    public function state_options()
    {
        $this->set_up_civi($result);
        $this->set_state_options($result, $options);
        return  $options;
    }
    public function set_up_civi(&$result)
    {
        $civi = new Civicrm(); $civi->initialize();
        $this->civicrm = new civi_query($civi);
        $this->civicrm->mode = 'get'; $this->civicrm->entity = 'StateProvince';
        $this->civicrm->params = array(
            'sequential' => 1,
            'country_id' => "1228",
            'options' => ['limit' => 60],
        );
        $this->civicrm->civi_query($result);
    }
    public function set_state_options($result, &$options)
    {
        foreach($result['values'] as $state)
        {
            if($state['id'] != "1052" && $state['id'] != "1053" &&$state['id'] != "1055"
                && $state['id'] != "1057" && $state['id'] != "1058" && $state['id'] != "1059"
                && $state['id'] != "1060" && $state['id'] != "1061"){
                $options[$state['abbreviation']] = $state['name'];}
        }
        ksort($options);
    }
}