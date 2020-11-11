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
    public $ajax_array;
    public function get_issue_id(){
    return $this->issue_id;
    }
    public function get_sql_result()
    {return $this->sql_result;}
    public function get_ajax_array()
    {return $this->ajax_array;}
    public $database;
    public function request_js_data()
    {
        $request = Request::createFromGlobals();
        $this->issue_id = $request->request->get('issueid');
    }
    public function issue_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_issues where issue_id '".$this->get_issue_id()."';";
        $key = "issue_id";
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
        $this->database = null;
    }
    public function process_sql_result()
    {
        $issue_data = get_object_vars($this->get_sql_result()[$this->get_issue_id()]);
        $ajax_array = [];
        $ajax_array[0] = $issue_data['issue_name'];
        $ajax_array[1] = $issue_data['bill_id'];
        $jajx_array[2] = $issue_data['bill_slug'];

    }

}