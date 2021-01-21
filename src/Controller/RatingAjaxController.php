<?php
Namespace Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class  RatingAjaxController extends  ControllerBase
{
    public $meeting_id;
    public $sql_result;
    public $data;
    public function get_meeting_id(){
        return $this->meeting_id;
    }
    public function get_sql_result()
    {return $this->sql_result;}
    public function get_data()
    {return $this->data;}
    public $database;
    public function content()
    {
        $this->set_data();
        return new JsonResponse($this->get_data());
    }

    public function request_js_data()
    {
        $request = Request::createFromGlobals();
        $this->meeting_id = $request->request->get('meetingid');

    }
    public function set_data()
    {
        $this->request_js_data();
        $this->find_ratings();
        $this->loop();

    }
    public function find_ratings()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_rating where activity_id = '".$this->get_meeting_id()."'
        order by issue_id ASC;";
        $key = 'issue_id';
        $this->database->select_query($query, $key);
        $this->sql_result = $this->database->get_result();
        $this->database = null;
    }
    public function loop()
    {
        $count = 1;
        foreach ($this->get_sql_result() as $rating)
        {
            $rating = get_object_vars($rating);
            switch ($count)
            {
                case 1:
                    $this->set_issue_1($rating, $data);  break;
                case 2:
                    $this->set_issue_2($rating, $data);  break;
                case 3:
                    $this->set_issue_3($rating, $data);  break;
                case 4:
                    $this->set_issue_4($rating, $data);break;
                case 5:
                    $this->set_issue_5($rating, $data); break;
            }
            $count++;
        }
        $this->data = $data;
    }
    public function set_issue_1($rating, &$data)
    {
        $new_rating = $rating['rating'];
        $this->convert_rating($new_rating);
        $data[0] = $new_rating;
        $data[1] = $rating['comment'];
    }
    public function set_issue_2($rating, &$data)
    {
        $new_rating = $rating['rating'];
        $this->convert_rating($new_rating);
        $data[2] = $new_rating;
        $data[3] = $rating['comment'];
    }
    public function set_issue_3($rating, &$data)
    {
        $new_rating = $rating['rating'];
        $this->convert_rating($new_rating);
        $data[4] = $new_rating;
        $data[5] = $rating['comment'];
    }
    public function set_issue_4($rating, &$data)
    {
        $new_rating = $rating['rating'];
        $this->convert_rating($new_rating);
        $data[6] = $new_rating;
        $data[7] = $rating['comment'];
    }
    public function set_issue_5($rating, &$data)
    {
        $new_rating = $rating['rating'];
        $this->convert_rating($new_rating);
        $data[8] = $new_rating;
        $data[9] = $rating['comment'];
    }


    public function convert_rating(&$new_rating)
    {
        switch($new_rating)
        {
            case 'y':
                $rating = "Yes"; break;
            case "n":
                $rating = "No"; break;
            case "u":
                $rating = "Undecided"; break;
            case "nd":
                $rating = "Not Discussed"; break;
        }
        $new_rating = $rating;
    }

}