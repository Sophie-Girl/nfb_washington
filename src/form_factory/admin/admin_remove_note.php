<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\nfb_washington\database\base;
use Drupal\civicrm\Civicrm;
use Drupal\nfb_washington\civicrm\civi_query;
class  admin_remove_note
{
    public $database;
    public $civicrm;
    public $note_link_id;
    public function get_note_link_id()
    {return $this->note_link_id;}
    public $note_id;
    public function get_note_id()
    {return $this->note_id;}
    public $member_id;
    public function get_member_id()
    {return $this->member_id;}
    public $note_text;
    public function  get_note_text()
    {return $this->note_text;}
    public $civicrm_id;
    public function get_civicrm_id()
    {return $this->civicrm_id;}
    public $first_name;
    public function get_first_name()
    {return $this->first_name;}
    public $last_nane;
    public function get_last_name()
    {return $this->last_nane;}
    public function build_form_array(&$form, $form_state, $link)
    {
        if($link != "na")
        {
            $this->note_link_id = $link;
            $this->find_note_and_member_id();
            $this->build_link_id($form, $form_state);
            $this->note_markup($form, $form_state);
            $this->confirmation($form, $form_state);
        }
        else {
            $form['oops_markup'] = array(
              '#type' => 'item',
              '#markup' => "<p>oops you shouldn't see this</p>"
            );
        }
    }
    public function find_note_and_member_id()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_note_link where link_id = '".$this->get_note_link_id()."';";
        $key = 'link_id';
        $this->database->select_query($query, $key);
        foreach($this->database->get_result() as $link)
        {
            $link = get_object_vars($link);
            $this->note_id = $link['note_id'];
            $this->member_id = $link['entity_id'];
        }
        $this->database = null;
        $this->find_note_text();
        $this->find_member();
        $this->get_first_name_last_name();
    }
    public function find_note_text()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_note where note_id = '".$this->get_note_id()."';";
        $key = 'note_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $note)
        {
            $note = get_object_vars($note);
            $this->note_text = $note['note'];
        }
        $this->database = null;
    }
    public function  find_member()
    {
        $this->database = new base();
        $query = "select * from nfb_washington_members where member_id = '".$this->get_member_id()."';";
        $key = 'member_id';
        $this->database->select_query($query, $key);
        foreach ($this->database->get_result() as $member)
        {
            $member = get_object_vars($member);
            $this->civicrm_id = $member['civicrm_contact_id'];
        }
        $this->database = null;
    }
    public function  get_first_name_last_name()
    {
        $civicrm = new Civicrm(); $civicrm->initialize();
        $this->civicrm = new civi_query($civicrm);
        $this->civicrm->civi_entity = "Contact";
        $this->civicrm->civi_mode = 'get';
        $this->civicrm->civi_params = array(
            'sequential' => 1,
            'id' => $this->get_civicrm_id(),
        );
        $this->civicrm->civi_query();
        foreach($this->civicrm->get_civicrm_result()['values'] as $contact)
        {
            $this->first_name = $contact['first_name'];
            $this->last_nane = $contact['last_name'];
        }
        $this->civicrm = null;
    }
    public function build_link_id(&$form, &$form_state)
    {
        $form['link_value'] = array(
            '#type' => 'textfield',
            '#value' => $this->get_note_link_id(),
            '#size' => '20',
            '#attributes' => array('readonly' => 'readonly'),
            '#title' => "Note Link ID"
        );
    }
    public function note_markup(&$form,$form_state)
    {
        $form['note_markup'] = array(
          '#type' => 'item',
          '#markup' => "<p>Bellow is the note you wish to remove from ".$this->get_first_name()." ".$this->get_last_name()."
          </p><p>".$this->get_note_text()."</p>"
        );
    }
    public function confirmation(&$form, $form_state)
    {
        $form['confirm'] = array(
            '#type' => 'checkbox',
            '#title' =>'I confirm I want to remove this note',
            '#required' => 'true',
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => 'Submit',
            '#states' => [
                'visible' =>[
                    [':input[name="confirm"]' => ['checked' => true]]],],
        );
    }





}