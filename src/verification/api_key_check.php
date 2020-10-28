<?php
Namespace Drupal\nfb_washington\verification;
use Drupal\nfb_washington\database\base;

class  api_key_check
{
    public $apikey;
    public function get_apikey()
    {return $this->apikey;}
    public $status;
    public function get_status()
    {return $this->status;}
    public $database;
    public function api_key_ckeck()
    {
        $api_key = null;
        $this->database = new base();
        $query = "select * from nfb_washington_config where setting = 'pp_id' and active = '0';";
        $key = 'config_id';
        $this->database->select_query($query, $key);
        \Drupal::logger('nfb_washington')->notice(print_r($this->database->get_result(), true));
        if($this->database->get_result() != "error"){
        foreach($this->database->get_result() as $setting)
        {
            $setting = get_object_vars($setting);
            $api_key = $setting['value'];
        }}
        $this->apikey = $api_key;
    }
    public function  api_key_validation(&$form, &$form_state)
    {
        $this->api_key_ckeck();
        if($this->get_apikey() == null)
        {$form['warning_markup'] =array(
          '#type' => 'item',
          '#markup' => "<h2 class='admin_alert'> No Propublica API-key Set</h2>
<p>There is no Propublica API key set. u cannot pull data from Propublica to populate/update 
Members of Congress, Issues/Bills, Committees and membership, etc. Please Visit <a href='/nfb_washington/admin/configuration'>the 
NFB Washington configuration page</a> to add or update one. </p>"
        );

        $this->status = "false";}
        else
        {
            $this->status = "true";
        }
    }



}