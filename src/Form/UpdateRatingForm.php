<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\form_factory\form_factory;
use Drupal\nfb_washington\post_process\new_ratings_form_backend;

class UpdateRatingForm extends FormBase
{
    private $form_factory;
    private $post_process;
    public $issue_count;
    public function get_issue_count()
    {return $this->issue_count;}

    public function getFormId()
    {
        return "wash_sem_update_issue_rank";
    }

    public function buildForm(array $form, FormStateInterface $form_state, $rating = "new")
    {
        $form['#attached']['library'][] = 'nfb_washington/updaterate';
        $this->form_factory = new form_factory();
        $this->form_factory->build_update_rating_form($form, $form_state, $rating);
        $this->form_factory = null;
        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/ease-of-use';
        $form['#attached']['library'][] = 'nfb_washington/ease-of-use';
        $this->post_process = new new_ratings_form_backend();
        $this->post_process->backend($form_state);
        $this->post_process = null;

        \Drupal::messenger()->addMessage($this->t("Rating Submitted"));
    }

    public function staterep_refresh(&$form, $form_state)
    {
        return $form['select_rep'];
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {

        parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
        $this->set_issue_count();
        $this->prevent_links($form_state);
        $this->prevent_carrots($form_state);
        $this->and_check($form_state);
        $this->prevent_TBD($form_state);
        $this->prevent_semi_colon( $form_state);
        $this->prevent_sql_injection($form_state);
    }
    public function prevent_sql_injection($form_state)
    {
        $check = " ".strtolower($form_state->getValue("nfb_contact_name"));
        if(strpos($check, "drop table") > 0)
        {
            $form_state->setErrorByName("nfb_contact_name", "No Semi-colons");
        }
        $check = " ".strtolower($form_state->getValue("nfb_civicrm_phone_1"));
        if(strpos($check, "drop table") > 0)
        {
            $form_state->setErrorByName("nfb_civicrm_phone_1", "No Semi-colons");
        }
        $check = " ".strtolower($form_state->getValue("issue_1_comment"));
        if(strpos($check, "drop table") > 0)
        {
            $form_state->setErrorByName("issue_1_comment", "No Semi-colons");
        }
        $check = " ".strtolower($form_state->getValue("issue_2_comment"));
        if(strpos($check, "drop table") > 0)
        {
            $form_state->setErrorByName("issue_2_comment", "No Semi-colons");
        }
        $check = " ".strtolower($form_state->getValue("issue_3_comment"));
        if(strpos($check, "drop table") > 0)
        {
            $form_state->setErrorByName("issue_3_comment", "No Semi-colons");
        }
        $check = " ".strtolower($form_state->getValue("issue_4_comment"));
        if(strpos($check, "drop table") > 0)
        {
            $form_state->setErrorByName("issue_4_comment", "No Semi-colons");
        }
        $check = " ".strtolower($form_state->getValue("issue_5_comment"));
        if(strpos($check, "drop table") > 0)
        {
            $form_state->setErrorByName("issue_5_comment", "No Semi-colons");
        }
    }
    public function prevent_TBD(FormStateInterface $form_state)
    {
        $check = $form_state->getValue("nfb_civicrm_phone_1");
        if(strpos(" ".$check, "TBD")  > 0)
        {
            $form_state->setErrorByName("nfb_civicrm_phone_1", "Please Remove the previous entry, what you have submitted is too long");
        }
        if(strlen($check) > 12)
        {
            $form_state->setErrorByName("nfb_civicrm_phone_1", "What you have submitted is too long");
        }
    }
    public function prevent_semi_colon(FormStateInterface  $form_state)
    {
        $check = " ".$form_state->getValue("nfb_contact_name");
        if(strpos($check, ";") > 0)
        {
            $form_state->setErrorByName("nfb_contact_name", "No Semi-colons");
        }
        $check = " ".$form_state->getValue("nfb_civicrm_phone_1");
        if(strpos($check, ";") > 0)
        {
            $form_state->setErrorByName("nfb_civicrm_phone_1", "No Semi-colons");
        }
        $check = " ".$form_state->getValue("issue_1_comment");
        if(strpos($check, ";") > 0)
        {
            $form_state->setErrorByName("issue_1_comment", "No Semi-colons");
        }
        $check = " ".$form_state->getValue("issue_2_comment");
        if(strpos($check, ";") > 0)
        {
            $form_state->setErrorByName("issue_2_comment", "No Semi-colons");
        }
        $check = " ".$form_state->getValue("issue_3_comment");
        if(strpos($check, ";") > 0)
        {
            $form_state->setErrorByName("issue_3_comment", "No Semi-colons");
        }
        $check = " ".$form_state->getValue("issue_4_comment");
        if(strpos($check, ";") > 0)
        {
            $form_state->setErrorByName("issue_4_comment", "No Semi-colons");
        }
        $check = " ".$form_state->getValue("issue_5_comment");
        if(strpos($check, ";") > 0)
        {
            $form_state->setErrorByName("issue_5_comment", "No Semi-colons");
        }

    }
    public function prevent_links(FormStateInterface $form_state)
    {
        $check = $form_state->getValue("issue_1_comment");
        $id = "issue_1_comment";
        $this->slash_check($check, $form_state, $id);
        $this->com_check($check, $form_state, $id);
        $this->https_check($check, $form_state, $id);
        if($this->get_issue_count() > 1)
        {
            $check = $form_state->getValue("issue_2_comment");
            $id = "issue_2_comment";
            $this->slash_check($check, $form_state, $id);
            $this->com_check($check, $form_state, $id);
            $this->https_check($check, $form_state, $id);
        }
        if($this->get_issue_count() > 3)
        {
            $check = $form_state->getValue("issue_3_comment");
            $id = "issue_3_comment";
            $this->slash_check($check, $form_state, $id);
            $this->com_check($check, $form_state, $id);
            $this->https_check($check, $form_state, $id);
        }
        if($this->get_issue_count() > 3)
        {
            $check = $form_state->getValue("issue_4_comment");
            $id = "issue_4_comment";
            $this->slash_check($check, $form_state, $id);
            $this->com_check($check, $form_state, $id);
            $this->https_check($check, $form_state, $id);
        }
        if($this->get_issue_count() > 4)
        {
            $check = $form_state->getValue("issue_5_comment");
            $id = "issue_5_comment";
            $this->slash_check($check, $form_state, $id);
            $this->com_check($check, $form_state, $id);
            $this->https_check($check, $form_state, $id);
        }
    }
    public function slash_check($check, FormStateInterface $form_state, $id)
    {
        $slash = strpos(" ".$check, "/");
        if($slash > 0)
        {
            $form_state->setErrorByName($id, "/ is not a valid character. Please remove and try again");
        }
    }
    public function com_check($check, FormStateInterface $form_state, $id)
    {
        $com = strpos(" ".$check, ".com");
        if($com > 0)
        {
            $form_state->setErrorByName($id, "Hyperlinks are not a valid entry");
        }
    }
    public function https_check($check, FormStateInterface  $form_state, $id)
    {
        $https = strpos(" ".$check, "http");
        if($https > 0)
        {
            $form_state->setErrorByName($id, "Hyperlinks are not a valid entry");
        }
    }
    public function prevent_carrots(FormStateInterface  $form_state)
    {
        $check = $form_state->getValue("issue_1_comment");
        $carrot = strpos(" ".$check, "<");
        $close_carrot = strpos(" ".$check, ">");
        if($carrot > 0)
        {
            $form_state->setErrorByName("issue_1_comment", "Illegal character choice in <. Please remove");
        }
        if($close_carrot > 0 )
        {
            $form_state->setErrorByName("issue_1_comment", "Illegal character choice in >. Please remove");
        }
        if($this->get_issue_count() > 1){
            $check = $form_state->getValue("issue_2_comment");
            $carrot = strpos(" ".$check, "<");
            $close_carrot = strpos(" ".$check, ">");
            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_2_comment", "Illegal character choice in <. Please remove");
            }
            if($close_carrot > 0 )
            {
                $form_state->setErrorByName("issue_2_comment", "Illegal character choice in >. Please remove");
            }
        }
        if($this->get_issue_count() > 2){
            $check = $form_state->getValue("issue_3_comment");
            $carrot = strpos(" ".$check, "<");
            $close_carrot = strpos(" ".$check, ">");
            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_3_comment", "Illegal character choice in <. Please remove");
            }
            if($close_carrot > 0 )
            {
                $form_state->setErrorByName("issue_3_comment", "Illegal character choice in >. Please remove");
            }
        }
        if($this->get_issue_count() > 3){
            $check = $form_state->getValue("issue_4_comment");
            $carrot = strpos(" ".$check, "<");
            $close_carrot = strpos(" ".$check, ">");
            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_4_comment", "Illegal character choice in <. Please remove");
            }
            if($close_carrot > 0 )
            {
                $form_state->setErrorByName("issue_4_comment", "Illegal character choice in >. Please remove");
            }
        }
        if($this->get_issue_count() > 4){
            $check = $form_state->getValue("issue_5_comment");
            $carrot = strpos(" ".$check, "<");
            $close_carrot = strpos(" ".$check, ">");
            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_5_comment", "Illegal character choice in <. Please remove");
            }
            if($close_carrot > 0 )
            {
                $form_state->setErrorByName("issue_5_comment", "Illegal character choice in >. Please remove");
            }
        }
    }
    public function and_check(FormStateInterface  $form_state)
    {
        $check = $form_state->getValue("issue_1_comment");
        $carrot = strpos(" ".$check, "&");

        if($carrot > 0)
        {
            $form_state->setErrorByName("issue_1_comment", "Illegal character choice in &. Please remove");
        }
        if($this->get_issue_count() > 1)
        {
            $check = $form_state->getValue("issue_2_comment");
            $carrot = strpos(" ".$check, "&");

            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_2_comment", "Illegal character choice in &. Please remove");
            }
        }
        if($this->get_issue_count() > 2)
        {
            $check = $form_state->getValue("issue_3_comment");
            $carrot = strpos(" ".$check, "&");

            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_3_comment", "Illegal character choice in &. Please remove");
            }
        }
        if($this->get_issue_count() > 3)
        {
            $check = $form_state->getValue("issue_4_comment");
            $carrot = strpos(" ".$check, "&");

            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_4_comment", "Illegal character choice in &. Please remove");
            }
        }
        if($this->get_issue_count() > 4)
        {
            $check = $form_state->getValue("issue_5_comment");
            $carrot = strpos(" ".$check, "&");

            if($carrot > 0)
            {
                $form_state->setErrorByName("issue_5_comment", "Illegal character choice in &. Please remove");
            }
        }

    }
    public function set_issue_count()
    {
        $issue_count = null;
        $query = "select * from nfb_washington_config where setting = 'issue_count' and active = '0';";
        $key = 'config_id';
        $this->database = new base();
        $this->database->select_query($query, $key);
        if($this->database->get_result() != "error"|| $this->database->get_result() != array())
        {
            foreach($this->database->get_result() as $setting)
            {
                if($issue_count == null){
                    $setting = get_object_vars($setting);
                    $issue_count = $setting['value'];}
            }
        }
        $this->issue_count = $issue_count;
        $this->database = null;

    }

}