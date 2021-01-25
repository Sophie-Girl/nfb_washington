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
                    $data[4] = $config['value']; break;
            }
        }
        $this->data = $data;

    }

}