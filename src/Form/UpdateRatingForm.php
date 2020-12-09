<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\form_factory\form_factory;
use Drupal\nfb_washington\post_process\new_ratings_form_backend;

class UpdateRatingForm extends FormBase
{
    private $form_factory;
    private $post_process;

    public function getFormId()
    {
        return "wash_sem_update_issue_rank";
    }

    public function buildForm(array $form, FormStateInterface $form_state, $rating = "new")
    {
        $this->form_factory = new form_factory();
        $this->form_factory->build_update_rating_form($form, $form_state, $rating);
        $this->form_factory = null;
        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->post_process = new new_ratings_form_backend();
        $this->post_process->backend($form_state);
        $this->post_process = null;
        drupal_set_message($this->t("Rating Submitted"), 'status');
    }

    public function staterep_refresh(&$form, $form_state)
    {
        return $form['select_rep'];
    }
}