<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\Core\Form\FormStateInterface;

class all_ind_report
{

    public function  build_form_array(&$form, FormStateInterface $form_state)
    {
        $form['report_markup'] = array(
            '#type' => 'item',
            '#markup' => "Creates a large word doc with all members of congress for brailling"
         );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );

    }
}