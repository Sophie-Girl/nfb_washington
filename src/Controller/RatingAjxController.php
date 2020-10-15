<?php
Namespace Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\archive_nfb\activity_data;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RatingAjxController extends ControllerBase
{
 public $archive;
 public $rep_id;
 public $ratings_array;
 public $result_array;
 public function  get_rep_id()
 {return $this->rep_id;}
 public function get_ratings_array()
 {return $this->ratings_array;}
 public function get_results_array()
 {return $this->result_array;}
 public function content()
 {
     return json_encode($this->get_data_for_result());
 }
 public function  dependency_injection()
 {
     $archive = new activity_data();
     return $archive;

 }
 public function recover_rep_id()
 {
     $request = Request::createFromGlobals();
     $this->rep_id =  $request->request->get('rep_id');
 }
 public function get_meeting_details()
 {
     $this->archive = $this->dependency_injection();
     $sem_id = $this->get_rep_id();
     $this->archive->find_rep_name($sem_id, $array);
     $this->result_array = $array;
 }
 public function parse_result_array()
 {
     if($this->get_results_array()['issue1'] != "no rating")
     {$ratings_array['0'] = $this->get_results_array()['issue1'];}
     else{$ratings_array['0'] = "no_value";}
     if($this->get_results_array()['issue2'] != "no rating")
     {$ratings_array['1'] = $this->get_results_array()['issue2'];}
     else{$ratings_array['1'] = "no_value";}
     if($this->get_results_array()['issue3'] != "no rating")
     {$ratings_array['2'] = $this->get_results_array()['issue3'];}
     else{$ratings_array['2'] = "no_value";}
     $ratings_array['3'] = $this->get_results_array()['comment1'];
     $ratings_array['4'] = $this->get_results_array()['comment2'];
     $ratings_array['5'] = $this->get_results_array()['comment3'];
 }
 public function get_data_for_result()
 {
     $this->recover_rep_id();
     $this->get_meeting_details();
     $this->parse_result_array();
     return $this->get_ratings_array();
 }


}