<?php
Namespace Drupal\nfb_washington\post_process\admin;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Symfony\Component\HttpFoundation\RedirectResponse;
class admin_set_meetings {
    /*################################################################
     *# Connell, Sophia: Creating this script as a solution to the  #
     *# new request for reports on empty meetings. This will gen    #
     *# empty token meetings that will allow for easy reporting      #
      ################################################################ */
    public $database;
    public $year;
    public function get_year()
    {return $this->year;}
    public function set_year()
    {
        $this->year = date('Y');
    }
    public function create_dummy_meetings()
    {
        $this->set_year();

    }
    public function grab_all_active()
    {
        $query = "SELECT * FROM nfb_washington_members where nfb_washington_members.active != 1;";
        $this->database = new base();
        $key = 'member_id';
        $this->database->select_query($query, $key);

    }



}