<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\RedirectResponse;

class new_ratings_form_backend
{
    public $database;
    public $issue1;
    public $issue2;
    public $issue3;
    public $member_id;
    public $meeting_id;
    public $rating_id;
    public $nfb_contact;
    public $nfb_phone;
    public $issue_1_rating;
    public $issue_1_comment;
    public $issue_2_rating;
    public $issue_2_comment;
    public $issue_3_rating;
    public $issue_3_comment;
    public function get_issue_1()
    {return $this->issue1;}
    public function get_issue_2()
    {return $this->issue2;}
    public function get_issue_3()
    {return $this->issue3;}
    public function get_meeting_id()
    {return $this->meeting_id;}
    public function get_member_id()
    {return $this->member_id;}
    public function get_rating_id()
    {return $this->rating_id;}
    public function get_nfb_contact()
    {return $this->nfb_contact;}
    public function get_nfb_phone()
    {return $this->nfb_phone;}
    public function  get_issue_1_rating()
    {return $this->issue_1_rating;}
    public function  get_issue_1_comments()
    {return $this->issue_1_comment;}
    public function  get_issue_2_rating()
    {return $this->issue_2_rating;}
    public function  get_issue_2_comments()
    {return $this->issue_2_comment;}
    public function  get_issue_3_rating()
    {return $this->issue_3_rating;}
    public function  get_issue_3_comments()
    {return $this->issue_3_comment;}
    public function backend(FormStateInterface $form_state)
    {
        $this->set_issue_ids();
        $this->set_values($form_state);
        if($this->get_meeting_id() == null) {
            $this->find_meeting_id();
            if ($this->get_meeting_id() == null) {
                $this->find_meeting_id(); /*Connell Sophia Find freshly created id */
            }
        }
        $issue_number = 1;
        $this->deduplication($issue_number);
        $issue_number = 2;
        $this->deduplication($issue_number);
        $issue_number = 3;
        $this->deduplication($issue_number);


    }
    public function set_values(FormStateInterface $form_state)
    {
        if($form_state->getValue("rating_value") == "new")
        { $this->meeting_id = null;
          $this->member_id = $form_state->getValue("select_rep");
        }
        else {
            $this->meeting_id =  $form_state->getValue("rating_value");
            $this->member_id = null;
        }
        $this->nfb_contact = $form_state->getValue("nfb_contact_name");
        $this->nfb_phone = $form_state->getValue("nfb_civicrm_phone_1");
        $this->issue_1_rating = $form_state->getValue("issue_1_ranking");
        $this->issue_1_comment = $form_state->getValue("issue_1_comment");
        $this->issue_2_rating = $form_state->getValue("issue_2_ranking");
        \Drupal::logger("nfb_washington")->notice("issue_2_rating ".$this->get_issue_2_rating());
        $this->issue_2_comment = $form_state->getValue("issue_2_comment");
        $this->issue_3_rating = $form_state->getValue("issue_3_ranking");
        $this->issue_3_comment = $form_state->getValue("issue_3_comment");
    }
    public function find_meeting_id()
    {
        $this->database = new base();
        $year = date("Y");
        $query = "select * from nfb_washington_activities where member_id = '" . $this->get_member_id() . "' and  meeting_year = '" . $year . "';";
        $key = 'activity_id';
        $activity_id = null;
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $meeting)
        {
            $meeting = get_object_vars($meeting);
            if($activity_id == null)
            {$activity_id = $meeting['activity_id'];}
        }
        if($activity_id == null)
        {
            $this->create_meeting();
        }
        else
        {$this->meeting_id = $activity_id;}
    }
    public function create_meeting()
    {
        $this->database = new base();
        $year = date('Y');
        $this->database = new base();
        $feilds = array(
            "member_id" => $this->get_member_id(),
            "type" => "meeting",
            "meeting_date" => '1/1/2020',
            "meeting_time" => "12:00: Am",
            "description" => "Washington Seminar Meeting",
            "location" => "Unknown",
            "m_o_c_contact" => "Unknown",
            "nfb_contact" => $this->get_nfb_contact(),
            "nfb_phone" => $this->get_nfb_phone(),
            "moc_attendance" => "1",
            "meeting_year" => $year,
            "created_user" => \Drupal::currentUser()->getUsername(),
            "last_modified_user" => \Drupal::currentUser()->getUsername(),
        );
        $table = "nfb_washington_activities";
        $this->database->insert_query($table, $feilds);
        $this->database = null;
    }
    public function set_issue_ids()
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
                    $this->issue1 = $issue['issue_id'];
                    break;
                case 2:
                    $this->issue2 = $issue['issue_id'];
                    break;
                case 3:
                    $this->issue3 = $issue['issue_id'];
                    break;
            }
            $count++;
        }
    }
    public function deduplication($issue_number)
    {
        $this->database = new base();
        switch ($issue_number)
        {
            case 1:
                $query = "select * from  nfb_washington_rating where activity_id = '".$this->get_meeting_id()."' and 
                issue_id = '".$this->get_issue_1()."';"; break;
            case 2:
                $query = "select * from  nfb_washington_rating where activity_id = '".$this->get_meeting_id()."' and 
                issue_id = '".$this->get_issue_2()."';";break;
            case 3:
                $query = "select * from  nfb_washington_rating where activity_id = '".$this->get_meeting_id()."' and 
                issue_id = '".$this->get_issue_3()."';"; break;
        }
        $key = 'rating_id';
        $this->database->select_query($query, $key);
        $rating_id = null;
        foreach($this->database->get_result() as $rating)
        {
            $rating = get_object_vars($rating);
            if($rating_id == null)
            {$rating_id = $rating['rating_id'];}
        }
        if($rating_id == null)
        {
            $this->create_rating($issue_number);
        }
        else
        {
            $this->update_issue_rating($rating_id, $issue_number);
        }
    }
    public function create_rating($issue_number)
    {
        $this->database = new base();
        $this->fields_switch($issue_number, $fields);
        $table = "nfb_washington_rating";
        $this->database->insert_query($table, $fields);
    }
    public function update_issue_rating($rating_id, $issue_number)
    {
        switch($issue_number)
        {
            case 1:
                $rating = $this->get_issue_1_rating(); $this->convert_rating($rating, $issue_number);
                $this->update_issue_1($rating_id); break;
            case 2:
                $rating = $this->get_issue_2_rating(); $this->convert_rating($rating, $issue_number);
                $this->update_issue_2($rating_id); break;
            case 3:
                $rating = $this->get_issue_3_rating(); $this->convert_rating($rating, $issue_number);
                $this->update_issue_3($rating_id); break;
        }
    }
    public function fields_switch($issue_number, &$fields)
    {

        switch($issue_number)
        {
            case 1:
                $rating = $this->get_issue_1_rating(); $this->convert_rating($rating, $issue_number);
                $this->issue_1_create_array($fields); break;
            case 2:
                $rating = $this->get_issue_2_rating(); $this->convert_rating($rating, $issue_number);
                $this->issue_2_create_array($fields); break;
            case 3:
                $rating = $this->get_issue_3_rating(); $this->convert_rating($rating, $issue_number);
                $this->issue_3_create_array($fields); break;
        }
    }
    public function update_switch($issue_number)
    {

    }
    public function convert_rating($rating, $issue_number)
    {
        \Drupal::logger("nfb_wahsington")->notice("rating ".$rating);
        switch($rating)
        {
            case "Yes":
                $new_rating = "y"; break;
            case "No":
                $new_rating = "n"; break;
            case "Undecided":
                $new_rating = "u"; break;
            case "Not Discussed":
                $new_rating = "nd"; break;
        }
        switch($issue_number)
        {
            case 1:
                $this->issue_1_rating = $new_rating;
            case 2:
                $this->issue_2_rating = $new_rating;
            case 3:
                $this->issue_3_rating = $new_rating;
        }
    }
    public function issue_1_create_array(&$fields)
    {
        $fields = array(
            'activity_id' => $this->get_meeting_id(),
            'member_id' => $this->get_member_id(),
            'issue_id' => $this->get_issue_1(),
            'rating' => $this->get_issue_1_rating(),
            'comment' => $this->get_issue_1_comments(),
            'created_user' => \drupal::currentUser()->getUsername(),
            'last_modified_user' => \drupal::currentUser()->getUsername(),
            );
    }
    public function issue_2_create_array(&$fields)
    {
        $fields = array(
            'activity_id' => $this->get_meeting_id(),
            'member_id' => $this->get_member_id(),
            'issue_id' => $this->get_issue_2(),
            'rating' => $this->get_issue_2_rating(),
            'comment' => $this->get_issue_2_comments(),
            'created_user' => \drupal::currentUser()->getUsername(),
            'last_modified_user' => \drupal::currentUser()->getUsername(),
        );
    }
    public function issue_3_create_array(&$fields)
    {
        $fields = array(
            'activity_id' => $this->get_meeting_id(),
            'member_id' => $this->get_member_id(),
            'issue_id' => $this->get_issue_3(),
            'rating' => $this->get_issue_3_rating(),
            'comment' => $this->get_issue_3_comments(),
            'created_user' => \drupal::currentUser()->getUsername(),
            'last_modified_user' => \drupal::currentUser()->getUsername(),
        );
    }
    public function update_issue_1($rating_id)
    {
        $this->database = new base();
        $query = "update nfb_washington_rating
        set rating = '".$this->get_issue_1_rating()."'
        where rating_id = '".$rating_id."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_rating
        set comment = '".$this->get_issue_1_comments()."'
        where rating_id = '".$rating_id."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_rating
        set comment = '".$this->get_issue_1_comments()."'
        where last_modified_user = '".\Drupal::currentUser()->getUsername()."';";
        $this->database->update_query($query);
    }
    public function update_issue_2($rating_id)
    {
        $this->database = new base();
        $query = "update nfb_washington_rating
        set rating = '".$this->get_issue_2_rating()."'
        where rating_id = '".$rating_id."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_rating
        set comment = '".$this->get_issue_2_comments()."'
        where rating_id = '".$rating_id."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_rating
        set last_modified_user = '".\Drupal::currentUser()->getUsername()."'
        where  rating_id = '".$rating_id."';";
        $this->database->update_query($query);
    }
    public function update_issue_3($rating_id)
    {
        $this->database = new base();
        $query = "update nfb_washington_rating
        set rating = '".$this->get_issue_3_rating()."'
        where rating_id = '".$rating_id."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_rating
        set comment = '".$this->get_issue_3_comments()."'
        where rating_id = '".$rating_id."';";
        $this->database->update_query($query);
        $query = "update nfb_washington_rating
        set last_modified_user = '".\Drupal::currentUser()->getUsername()."'
        where  rating_id = '".$rating_id."';";
        $this->database->update_query($query);
    }

}