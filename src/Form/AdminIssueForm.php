<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\form_factory\admin\admin_issue;
use Drupal\nfb_washington\post_process\admin\admin_issue_backend;
use Drupal\nfb_washington\verification\api_key_check;
use Drupal\nfb_washington\verification\congress_number_check;

class AdminIssueForm extends FormBase
{
    public $verification;
    public $form_factory;
    public $backend;
    public $database;
    public function getFormId()
    {
       return "nmfb_washington_issue_admin";
    }
    public function  buildForm(array $form, FormStateInterface $form_state, $issue = "create")
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $form['#attached']['library'][] = 'nfb_washington/edit-issue';
        $this->verify_api_key($form, $form_state);
        $this->congress_number_markup($form, $form_state);
        $this->form_factory = new admin_issue();
        $this->form_factory->issue_switch($issue, $form, $form_state);
        $this->rule_of_three($form, $form_state);
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $issue = $form_state->getValue("issue_value");
        $this->backend = new admin_issue_backend();
        $this->backend->issue_backend($issue, $form_state);
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
        if($form_state->getValue("derivative_issue") == "na")
        {$form_state->setErrorByName("derivative_issue", "No issues have been set, so THis issue must be primary" );}
    }

    public function verify_api_key(&$form, $form_state)
    {
        $this->verification = new api_key_check();
        \drupal::logger('nfb_washington')->notice("i am about to run the query");
        $this->verification->api_key_validation($form,$form_state);
    }
    public function congress_number_markup(&$form, &$form_state)
    {
        $this->verification = new congress_number_check();
        $this->verification->congress_number_verification($form, $form_state);
        $this->verification = null;
    }
    public function rule_of_three(&$form, $form_state)
    {
        $this->database = new base(); $year = date("Y");
        $query = "select count(*)  as 'issues' from nfb_washington_issues where issue_year = '".$year."' group by issue_year;";
        $key = 'issues';
        $this->database->select_query($query, $key);
        $count = 0;
        foreach($this->database->get_result() as $count)
        {
            $count = get_object_vars($count);
        }
        if((int)$count > 2)
        {
            $this->too_many_issues($form, $form_state);
        }
        else {$this->go_ahead($form, $form_state);}
    }
    public function go_ahead(&$form, $form_state)
    {
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",
        );
    }
    public function too_many_issues(&$form, $form_state)
    {
        $form['alert_too_many'] = array(
          '#type' => "item",
          '#markup' => "<p class = 'admin_alert'>You have usbmitted all the issues you can for this year./ Please return to the 
<a href='/nfb_Washington/admin/issue'> issue home page</a></p>"
        );
    }

}