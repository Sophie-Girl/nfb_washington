<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;

class admin_note_creat_backend
{
    public $database;
    public $note_id;
    public $note_text;
    public $note_year;
    public $note_type;

    public function backend(FormStateInterface $form_state)
    {
        $this->set_values($form_state);
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

    }
}