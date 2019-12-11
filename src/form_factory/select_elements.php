<?php
namespace Drupal\nfb_washington\form_factory;
class select_elements extends textfield_elements
{
    public $options;
    public function get_element_options()
    {return $this->options;}
    public function build_Static_select_box(&$form)
    {   $form[$this->get_element_id()] = array(
        '#type' => $this->get_element_id(),
        '#title' => $this->t($this->get_element_title()), // do not stack translation trait elements
        '#options' => $this->get_element_options(),
        '#required' => $this->get_element_required_status(),);}
    public function MOC_select_options()
    {
        //todo establish way to get data out fo archive.nfb.org
    }
    public function Meeting_select_options()
    {
        //todo establish way to get data out fo archive.nfb.org
    }
    public function rankings_options()
    {
        $this->options = array(
          'Yes' => $this->t('Yes'),
          'No' => $this->t('No'),
          'Undecided' => $this->t('Undecided'),
          'Not Discussed' => $this->t('Not Discussed'),
        );
    }
    public function issue_1_ranking_element(&$form)
    {
        $this->rankings_options();
        $this->element_id = 'issue_1_ranking';
        $this->type = 'select';
        $this->title = 'Issue 1 Ranking';
        $this->required = True;
        $this->build_Static_select_box($form);
    }
    public function issue_2_ranking_element(&$form)
    {
        $this->rankings_options();
        $this->element_id = 'issue_2_ranking';
        $this->type = 'select';
        $this->title = 'Issue 2 Ranking';
        $this->required = True;
        $this->build_Static_select_box($form);
    }
    public function issue_3_ranking_element(&$form)
    {
        $this->rankings_options();
        $this->element_id = 'issue_3_ranking';
        $this->type = 'select';
        $this->title = 'Issue 3 Ranking';
        $this->required = True;
        $this->build_Static_select_box($form);
    }
    public function moc_select_element(&$form)
    { //todo implement this code once connection to archive.nfb.org is established
        }


}