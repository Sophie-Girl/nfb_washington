<?php
namespace  Drupal\nfb_washington\form_factory\report;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\microsoft_office\html_to_word;

class  directory extends rating_report
{
    public $form_factory_state_options;
    public function build_directory_form(&$form, FormStateInterface $form_state)
    {
         $this->form_factory_state_options = new \Drupal\nfb_washington\form_factory\select_elements();
         $this->form_factory_state_options->state_options();
         $option = $this->form_factory_state_options->get_element_options();
         $this->form_factory_state_options = null;
         $form['select_state'] = array(
           '#type' => 'select',
           '#title' => 'Select State Form Preview',
           '#options' => $option,
             '#ajax' => array(
                 'event' => 'change',
                 'wrapper' => 'directory',
                 'callback' => '::data_refresh'
             ),
         );
         $form['preview_markup'] = array(
           '#prefix' => "<div id = 'directory'>",
           '#type' => 'item',
           '#markup' => $this->directory_markup($form_state),
           "#suffix" => "</div>"
         );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Download",
        );

    }
    public function directory_markup(FormStateInterface $form_state)
    {
        if($form_state->getValue('select_state') == "")
        {$markup = "<p>You must select a state to se a preview of that state</p>";}
        else{
            $this->state = $form_state->getValue("select_state");
            $this->web_preview_markup();
            $markup = $this->get_markup();
        }
        return $markup;
    }
    public function web_preview_markup()
    {
        $this->markup = "<h2>".$this->get_state()." Members of Congress</h2>";
        $this->member_query();
        foreach ($this->get_member_results() as $member)
        {

            $this->set_member_values($member);
            $this->markup = $this->get_markup()."
            <p class='right-side'>".$this->get_first_name()." ".$this->get_last_name()." <span>".$this->district_text()."</span></p>
            <p>Phone: ".$this->get_phone()."</p>";
        }
    }
    public function directory_backend()
    {
        $year = date('Y');
        $this->markup = $year." Congressional Directory".PHP_EOL;
        $this->full_member_query();
        foreach ($this->get_member_results() as $member)
        {
            $this->set_member_values($member);
            $this->markup = $this->get_markup() . $this->get_first_name(). " ".$this->get_last_name().": "
                .$this->district_text().PHP_EOL.
                "Phone: ".$this->get_phone().PHP_EOL.
                "------------------------------------------------------".PHP_EOL;
        }
        $text = $this->get_markup();
        $this->phpoffice = new html_to_word();
        $this->phpoffice->report_name = "/var/www/html/drupal/web/sites/nfb.org/".$year."_congressional_directory.docx";
        $this->phpoffice->font_size = '12';
        $this->phpoffice->download_doc($text);
    }



}