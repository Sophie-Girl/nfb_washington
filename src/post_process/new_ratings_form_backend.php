<?php
Namespace Drupal\nfb_washington\post_process;
use Drupal\Core\Form\FormStateInterface;
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
    public function backend(FormStateInterface $form_state)
    {

    }
}