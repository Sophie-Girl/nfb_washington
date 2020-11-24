<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;

class admin_note_create_backend
{
    public $database;
    public $note_id;
    public $note_text;
    public $note_year;
    public $note_type;
    public function  get_note_id()
    {
        return $this->note_id;
    }
    public function get_note_text()
    {
        return $this->note_text;
    }
    public function get_note_year()
    {
        return $this->note_year;
    }
    public function get_note_type()
    {
        return $this->note_type;

    }
    public function backend(FormStateInterface $form_state)
    {
        $this->set_values($form_state);
        if($this->get_note_id() == "add")
        {
            $this-> duplicate_check();
        }
    }
    public function set_values(FormStateInterface $form_state)
    {
        $this->note_text = $form_state->getValue("note_text");
        $this->note_type = $form_state->getValue("note_type");
        $this->note_year = $form_state->getValue("note_year");
        $this->note_id = $form_state->getValue("note_value");
    }
    public function duplicate_check()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_note where note_text = '".$this->get_note_text()."' and
        note_type = '".$this->get_note_type()."' and note_year = '".$this->get_note_year()."';";
        $key = 'note_id';
        $this->database->select_query($query, $key);
        $note_id = null;
        foreach ($this->database->get_result() as  $note)
        {
            if($note_id == null)
            {$note_id = $note['note_id'];}
        }
        \Drupal::logger('nfb_washington_note')->notice("Note id: ".$note_id);
        if($note_id != null)
        {$this->note_id = $note_id;}
        else{
            $this->create_note_Record();
        }
        $this->database = null;
    }
    public function create_note_Record()
    {
        \Drupal::logger("nfb_washington_debug")->notice("i am getting her boi!");
        $this->database = new base();
        $fields = array(
          'note_type' => $this->get_note_type(),
          'note_text' => $this->get_note_text(),
          'note_year' => $this->get_note_year(),
          'created_user' => \Drupal::currentUser()->getUsername(),
          'modified_user' => \Drupal::currentUser()  ->getUsername(),
        );
        $table = 'nfb_washington_note';
        $this->database->insert_query($table, $fields);
        $this->database = null;
    }
}