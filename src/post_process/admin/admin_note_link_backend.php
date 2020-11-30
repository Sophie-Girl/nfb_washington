<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\RedirectResponse;

class admin_note_link_backend
{
    public $database;
    public $note_id;
    public $member_id;
    public function get_member_id()
    {return $this->member_id;}
    public function get_note_id()
    {return $this->note_id;}
    public function backend(FormStateInterface $form_state)
    {
        $this->set_values($form_state);
        $this->duplicate_check();
        drupal_set_message("Note Linked to Member");
        $ender = new RedirectResponse('/nfb_washington/admin/notes');
        $ender->send(); $ender = null;
        return;
    }
    public function set_values(FormStateInterface $form_state)
    {
        $this->member_id = $form_state->getValue("member");
        $this->note_id = $form_state->getValue("note_value");
    }
    public function duplicate_check()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_note_link;";
        $key = 'link_id';
        $this->database->select_query($query, $key);
        $link_id = null;
        foreach( $this->database->get_result() as $link)
        {
            $link = get_object_vars($link);
            if($link['table_name'] == "nfb_washington_members" && $link['entity_id']
            == $this->get_member_id() && $link['note_id'] == $this->get_note_id())
            {
                $link_id = $link['link_id'];
            }
        }
        if($link_id == null)
        {
            $this->create_link();
        }

    }
    public function create_link()
    {
        $this->database = new base();
        $fields = array(
            'table_name' => "nfb_washington_members",
            'entity_id' => $this->get_member_id(),
            'note_id' => $this->get_note_id(),
        );
        $table = "nfb_washington_note_link";
        $this->database->insert_query($table, $fields);
        $this->database = null;
    }

}