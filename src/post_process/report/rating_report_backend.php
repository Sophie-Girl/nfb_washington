<?php
namespace  Drupal\nfb_washington\post_process\report;
use Drupal\civicrm\Civicrm; // V3 will become deprecated.
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\database\base;
class rating_report_backend
{
    public $rating_id;
    public $issue_1_rating;
    public $issue_2_rating;
    public $issue_3_rating;
    public $issue_4_rating;
    public $issue_5_rating;
    public $issue_1_comment;
    public $issue_2_comment;
    public $issue_3_comment;
    public $issue_4_comment;
    public $issue_5_comment;
    public $issue_1_id;
    public $issue_2_id;
    public $issue_3_id;
    public $issue_4_id;
    public $issue_5_id;
    public $issue_1_name;
    public $issue_2_name;
    public $issue_3_name;
    public $issue_4_name;
    public $issue_5_name;
    public $issue_count;
    public $meeting_id;
    public$file_type;
    public $phpoffice;
    public function get_rating_id()
    {return $this->rating_id;}
    public function get_meeting_id()
    {return $this->meeting_id;}
    public function get_issue_1_rating()
    {return $this->issue_1_rating;}
    public function get_issue_2_rating()
    {return $this->issue_2_rating;}
    public function get_issue_3_rating()
    {return $this->issue_3_rating;}
    public function get_issue_4_rating()
    {return $this->issue_4_rating;}
    public function get_issue_5_rating()
    {return $this->issue_5_rating;}
    public function get_issue_1_comment()
    {return $this->issue_1_comment;}
    public function get_issue_2_comment()
    {return $this->issue_2_comment;}
    public function get_issue_3_comment()
    {return $this->issue_3_comment;}
    public function get_issue_4_comment()
    {return $this->issue_4_comment;}
    public function get_issue_5_comment()
    {return $this->issue_5_comment;}
    public function get_issue_1_id()
    {return $this->issue_1_id;}
    public function get_issue_2_id()
    {return $this->issue_2_id;}
    public function get_issue_3_id()
    {return $this->issue_3_id;}
    public function get_issue_4_id()
    {return $this->issue_4_id;}
    public function get_issue_5_id()
    {return $this->issue_5_id;}
    public function get_issue_1_name()
    {return $this->issue_1_name;}
    public function get_issue_2_name()
    {return $this->issue_2_name;}
    public function get_issue_3_name()
    {return $this->issue_3_name;}
    public function get_issue_4_name()
    {return $this->issue_4_name;}
    public function get_issue_5_name()
    {return $this->issue_5_name;}
    public function get_issue_count()
    {return $this->issue_count;}
    public function  get_file_type()
    {return $this->file_type;}


    public function file_download($file, $size, $filename)
    {
        if (strlen($file) > 0) {
            ob_start();  // buffer all but headers
            ob_end_clean();  // headers get sent, erase all buffering and enable output
            header("Content-type: application/csv; charset=UTF-8");
            header("Content-length: " . $size);
            header('Pragma: public');
            header("Content-Description: PHP Generated Data");
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $file;
            unlink($filename);
            exit;}
    }
}