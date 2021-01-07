<?php
namespace  Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\database\base;

class ConfigAjaxController extends  ControllerBase
{
    public $database;
    public $data;
    public function get_data()
    {return $this->data;}
    public function content()
    {

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

        }

    }

}