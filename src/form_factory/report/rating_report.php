<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\database\base;
use Drupal\nfb_washington\microsoft_office\html_to_word;

class rating_report extends meeting_report
{
    // Connell Sophie:

    public function build_rating_form(&$form, $form_state)
    {
        $form['intro_markup'] = array(
            '#type' => 'item',
            '#markup' => "<p>This report download will show  all meetings in order of the day in which they will happen in a word document</p>",
        );
        $form['file_type'] = array(
          '#type' => 'select',
          '#title' => "Select File Type",
          '#options' => array('csv' => "CSV Excel File",
              "docx" => "Word Document fro brailling"),
            '#required' => true
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );

    }
   }