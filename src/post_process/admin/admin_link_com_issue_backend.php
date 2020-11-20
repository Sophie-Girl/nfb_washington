<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\RedirectResponse;

class admin_link_com_issue_backend
{
    public $database;
    public $committee_id;
    public $issue_id;
    public function get_committee_id()
    {
        return $this->committee_id;
    }
    public function get_issue_id()
    {
        return $this->issue_id;
    }
    public function backend(FormStateInterface $form_state)
    {
            $this->set_values($form_state);
            $this->duplicate_check();
        \Drupal::logger("nfb_washington")->notice("I did the thing");
            $this->redirect();

    }
    public function set_values(FormStateInterface $form_state)
    {
        $this->committee_id = $form_state->getValue("committee_value");
        $this->issue_id = $form_state->getValue("issue_value");
    }
    public function duplicate_check()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_committee_issue_link where committee_id = '".$this->get_committee_id()."'
        and issue_id = '".$this->get_issue_id()."';";
        $key = "link_id";
        $this->database->select_query($query, $key);
        $link_id = null;
        foreach($this->database->get_result() as $link)
        {
            $link = get_object_vars($link);
            if($link_id == null)
            {
                $link_id = $link['link_id'];
            }
        }
        if($link_id == null)
        {
            $this->create_link();
        }
        $this->database = null;
    }
    public function create_link()
    {
        $this->database = new base();
        $fields = array(
            'committee_id' => $this->get_committee_id(),
            'issue_id' => $this->get_issue_id(),
        );
        $table = "nfb_washington_committee_issue_link";
        $this->database->insert_query($table, $fields);
    }
    public function redirect()
    {
        $message = "Committee linked to issue";
        drupal_set_message($message);
        $ender = new RedirectResponse('/nfb_washington/admin/committees');
        $ender->send(); $ender = null;
        return;
    }


}