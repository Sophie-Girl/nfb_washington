<?php
Namespace Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NoteEditAjaxController extends ControllerBase
{
    public $database;
    public $note_id;
    public $data;
    public function get_data()
    {return $this->data;}
    public function get_note_id()
    {
        return $this->note_id;
    }
    public function content()
    {
        $this->ajax_process();
        return new JsonResponse($this->get_data());
    }
    public function set_note_id()
    {
        $request = Request::createFromGlobals();
        $this->note_id = $request->request->get('noteid');
    }
    public function note_query()
    {
        $this->database= new base();
        $query = "select * nfb_washington_note where note_id = '".$this->get_note_id()."';";
        $key = 'note_id';
        $this->database->select_query($query, $key);
        $result = $this->database->get_result();
        $this->database = null;
        $this->build_data_array($result);
    }
    public function build_data_array($result)
    {
        $ddata = [];
        foreach ($result as $note)
        {
            $note = get_object_vars($note);
            $data[0] = $note['note_type'];
            $data[1] = $note['note_year'];
            $data[2] = $note['note_text'];
        }
        $this->data = $data;
    }
    public function ajax_process()
    {
        $this->set_note_id();
        $this->note_query();

    }
}