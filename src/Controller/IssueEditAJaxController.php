<?php
Namespace Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class  IssueEditAJaxController extends  ControllerBase
{
    public $issue_id;
    public $sql_result;
    public $data;
    public function get_issue_id(){
    return $this->issue_id;
    }
    public function get_sql_result()
    {return $this->sql_result;}
    public function get_data()
    {return $this->data;}
    public $database;
    public function content()
    {
        $this->get_ajax_data();
        \Drupal::logger("nfb_washington_ajax")->notice(print_r($this->get_data(), true));
        return new JsonResponse($this->get_data());
    }
    public function  get_ajax_data()
    {
        $this->request_js_data(); $this->issue_query();
        $this->process_sql_result();
    }
    public function request_js_data()
    {
        $request = Request::createFromGlobals();
        $this->issue_id = $request->request->get('issueid');
    }
    public function issue_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id = '".$this->get_issue_id()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
    }
    public function process_sql_result()
    {

        $issue_data = get_object_vars($this->get_sql_result()[$this->get_issue_id()]);
        \drupal::logger('nfb_washington_ajax')->notice(print_r($issue_data, true));
        $data = [];
        $data[0] = $issue_data['issue_name'];
        $data[1] = $issue_data['bill_id'];
        $data[2] = $issue_data['bill_slug'];
        if($issue_data['primary_status'] == "0")
        {$primary = "yes";}else{$primary = "no";}
        $data[3] = $primary;
        if($issue_data['primary_issue_id'])
        {$prim_id = $issue_data['primary_issue_id'];}
        else {$prim_id = "";}
        $data[4] = $prim_id;
        $this->data = $data;

    }

}