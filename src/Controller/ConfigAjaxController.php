<?php
namespace  Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class ConfigAjaxController extends  ControllerBase
{
    public $database;
    public $data;
    public function get_data()
    {return $this->data;}
    public function content()
    {
        $request = Request::createFromGlobals();
        $request->request->get('issueid');
        $this->config_values_query();
        return new JsonResponse($this->get_data());
    }
    public function config_values_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_config";
        $key = 'config_id';
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $config)
        {
            $config = get_object_vars($config);
            switch($config['setting'])
            {
                case 'pp_id':
                    $data[0] = $config['value']; break;
                case 'congress_number':
                    $data[1] = $config['value']; break;
                case 'seminar_type':
                    $data[2] = $config['value']; break;
                case 'staff_email':
                    $data[3]= $config['value']; break;
                case 'issue_count':
                    $this->issue_count_switch($issue_count, $config);
                    $data[4] = $issue_count; break;
            }
        }
        $this->data = $data;

    }
    public function  issue_count_switch(&$issue_count, $config)
    {
        switch ($config['value'])
        {
            case '1':
                $issue_count = 1; break;
            case '2':
                $issue_count = 2; break;
            case '3':
                $issue_count = 'original_spec'; break;
            case '4':
                $issue_count = 'death'; break;
            case '5':
                $issue_count = 'just_in_case';break;
        }
    }

}