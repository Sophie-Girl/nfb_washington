<?php
Namespace Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommitteeEditAjax extends ControllerBase
{
    public $committee_id;
    public $sql_result;
    public $data;
    public function get_committee_id()
    {return $this->committee_id;}
    public function get_sql_result()
    {return $this->sql_result;}
    public function get_data()
    {return $this->data;}
    public $database;
    public function content()
    {
        $this->ajax_response();
        return new JsonResponse($this->get_data());
    }
    public function request_js_data()
    {
        $request = Request::createFromGlobals();
        $this->committee_id = $request->request->get('committeeid');
    }
    public function  committee_search()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee where committee_id = '".$this->get_committee_id()."';";
        $key = "committee_id";
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
    }
    public function get_committee_values()
    {
        $result_array[0] = null;
        $result_array[1] = null;
        foreach ($this->get_sql_result() as $committee)
        {
            $committee = get_object_vars($committee);
            if($result_array[0] == null)
            {$result_array[0] = $committee['committee_name'];}
            if($result_array[1] == null)
            {$result_array[1] = $committee['propublica_id'];}
        }
        $this->data = $result_array;
    }
    public function ajax_response()
    {
        $this->request_js_data();
        $this->committee_search();
        $this->get_committee_values();
    }


}