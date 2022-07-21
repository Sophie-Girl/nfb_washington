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
        $form['filt_type'] = array(
            '#type' => 'select',
            '#title' => "Filter Report By",
            '#options' => array(
                'all' => "All Meetings",
                'state' => "Specific State",
                'unscheduled' => "Reps With No Meetings"
            ),
            '#required' => true,
        );
        $form['file_type'] = array(
          '#type' => 'select',
          '#title' => "Select File Type",
          '#options' => array('csv' => "CSV Excel File",
              "docx" => "Word Document fro brailling"),
            '#required' => true
        );
        $form['state_select'] = array(
            '#type' => "select",
            "#title" => "Select State",
            '#options' => $this->state_options(),
            '#states' => array(
                'visible' => [':input[name="filter_results"]' => ['value' => "state"]],
                'and',
                'required' => [':input[name="filter_results"]' => ['value' => "state"]],
            ),
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );

    }
   }