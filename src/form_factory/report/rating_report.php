<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;

class rating_report extends meeting_report
{
    // Connell Sophie: Uses many of the smae quieres as the meeting report so lets reuse it
    public $rating_id;
    public $issue_1_rating;
    public $issue_2_rating;
    public $issue_3_rating;
    public $issue_1_comment;
    public $issue_2_comment;
    public $issue_3_comment;
    public $issue_1_id;
    public $issue_2_id;
    public $issue_3_id;
    public $issue_1_name;
    public $issue_2_name;
    public $issue_3_name;
    public$file_type;
    public function get_rating_id()
    {return $this->rating_id;}
    public function get_issue_1_rating()
    {return $this->issue_1_rating;}
    public function get_issue_2_rating()
    {return $this->issue_2_rating;}
    public function get_issue_3_rating()
    {return $this->issue_3_rating;}
    public function get_issue_1_comment()
    {return $this->issue_1_comment;}
    public function get_issue_2_comment()
    {return $this->issue_2_comment;}
    public function get_issue_3_comment()
    {return $this->issue_3_comment;}
    public function get_issue_1_id()
    {return $this->issue_1_id;}
    public function get_issue_2_id()
    {return $this->issue_2_id;}
    public function get_issue_3_id()
    {return $this->issue_3_id;}
    public function get_issue_1_name()
    {return $this->issue_1_name;}
    public function get_issue_2_name()
    {return $this->issue_2_name;}
    public function get_issue_3_name()
    {return $this->issue_3_name;}
    public function  get_file_type()
    {return $this->file_type;}
    public function build_rating_form(&$form, $form_state)
    {
        $form['intro_markup'] = array(
            '#type' => 'item',
            '#markup' => "<p>This report download will show  all meetings in order of the day in which they will happen in a word document</p>",
        );
        $form['file_type'] = array(
          '#type' => 'select',
          '#title' => "Select File Type",
          '#options' => array('csv' => "CSV Excel File",
              "docx" => "Word Document fro brailling"),
            '#required' => true
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );

    }
    public function backend_markups_and_array(FormStateInterface $form_state)
    {
        $this->file_type = $form_state->getValue("file_type");
        $this->get_issues();
        $this->full_member_query();
        $ratings_array = [];
        foreach ($this->get_member_results() as $member)
        {
            $member = get_object_vars($member);
            $this->state = $member['state'];
            $this->rank = $member['rank'];
            $this->district = $member['district'];
            $this->civi_id = $member['civicrm_contact_id'];
            $this->civi_query_stuff();
            $this->rating_issue_1_query();
            $this->rating_issue_2_query();
            $this->rating_issue_3_query();
            $this->build_array($ratings_array);
        }
        if($this->get_file_type() == "csv")
        {
            // todo csv function
        }
        else {
            // todo word markup download
        }
    }
    public function rating_issue_1_query()
    {
         $this->database = new base();
         $query = "select * from nfb_washington_rating where member_id = '".$this->get_member_id()."' and issue_id = '".$this->get_issue_1_id()."';";
         $key = 'rating_id';
         $this->database->select_query($query, $key);
         if($this->database->get_result() == array()) {
             foreach ($this->database->get_result() as $rating) {
                 $rating = get_object_vars($rating);
                 $this->rating_switch($rating, $rating_value);
                 $this->issue_1_rating = $rating_value;
                 $this->issue_1_comment = $rating['comment'];
             }
         }

    }
    public function rating_issue_2_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_rating where member_id = '".$this->get_member_id()."' and issue_id = '".$this->get_issue_2_id()."';";
        $key = 'rating_id';
        $this->database->select_query($query, $key);
        if($this->database->get_result() == array()) {
            foreach ($this->database->get_result() as $rating) {
                $rating = get_object_vars($rating);
                $this->rating_switch($rating, $rating_value);
                $this->issue_2_rating = $rating_value;
                $this->issue_2_comment = $rating['comment'];
            }
        }

    }
    public function rating_issue_3_query()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_rating where member_id = '".$this->get_issue_3_id()."' and issue_id = '".$this->get_issue_1_id()."';";
        $key = 'rating_id';
        $this->database->select_query($query, $key);

        if($this->database->get_result() == array()) {
            foreach ($this->database->get_result() as $rating) {
                $rating = get_object_vars($rating);
                $this->rating_switch($rating, $rating_value);
                $this->issue_1_rating = $rating_value;
                $this->issue_1_comment = $rating['comment'];
            }
        }
    }
    public function rating_switch($rating, &$rating_value)
    {
        switch($rating['rating'])
        {
            case "y":
                $rating_value = "Yes"; break;
            case "n":
                $rating_value = "No"; break;
            case "u":
                $rating_value = "Undecided"; break;
            case "nd":
                $rating_value = "Not Discussed"; break;
        }
    }
    public function get_issues()
    {
        $this->database = new base(); $year = date("Y");
        $query = "select * from  nfb_washington_issues where issue_year = '".$year."'
         order by issue_id ASC";
        $key = 'issue_id';
        $this->database->select_query($query, $key);
        $count = 1;
        foreach ($this->database->get_result() as $issue)
        {
            $issue = get_object_vars($issue);
            switch ($count)
            {
                case 1:
                    $this->issue_1_name = $issue['issue_name'];
                    $this->issue_1_id = $issue['issue_id'];
                    break;
                case 2:
                    $this->issue_2_name = $issue['issue_name'];
                    $this->issue_2_id = $issue['issue_id'];
                    break;
                case 3:
                    $this->issue_3_name = $issue['issue_name'];
                    $this->issue_3_id = $issue['issue_id'];
                    break;
            }
            $count++;
        }
        $this->database = null;
    }
    public function build_array(&$ratings_array)
    {
        $array_key = $this->get_state().$this->get_last_name().$this->get_first_name();
        $ratings_array[$array_key]['first_name'] = $this->get_first_name();
        $ratings_array[$array_key]['last_name'] = $this->get_last_name();
        $ratings_array[$array_key]['phone'] = $this->get_phone();
        $ratings_array[$array_key]['district_text'] = $this->district_text();
        $ratings_array[$array_key][$this->get_issue_1_name()."_rating"] = $this->get_issue_1_rating();
        $ratings_array[$array_key][$this->get_issue_1_name()."_comment"] = $this->get_issue_1_comment();
        $ratings_array[$array_key][$this->get_issue_2_name()."_rating"] = $this->get_issue_2_rating();
        $ratings_array[$array_key][$this->get_issue_2_name()."_comment"] = $this->get_issue_2_comment();
        $ratings_array[$array_key][$this->get_issue_3_name()."_rating"] = $this->get_issue_3_rating();
        $ratings_array[$array_key][$this->get_issue_3_name()."_comment"] = $this->get_issue_3_comment();
    }

}