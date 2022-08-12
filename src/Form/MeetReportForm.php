<?php
Namespace Drupal\nfb_washington\Form;
use Drupal\civicrm\Civicrm;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfb_washington\civicrm\civicrm_v4;
use Drupal\nfb_washington\form_factory\report\meeting_report;
use Drupal\nfb_washington\microsoft_office\html_to_word;
use Drupal\nfb_washington\post_process\report\meeting_report_backend;
class MeetReportForm extends FormBase
{
    public $form_factory; public $backend;
    public function getFormId()
    {
        return 'meeting_report_nfb_washington';
    }
    public function  buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'nfb_washington/nfb-washington';
        $this->form_factory = new meeting_report();
        $this->form_factory->build_form($form, $form_state);
        $this->form_factory = null;
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $civicrm = new Civicrm(); $civicrm->initialize();
        $civicrm_v4 = new civicrm_v4($civicrm);
        $this->backend = new meeting_report_backend($civicrm_v4);
        $this->backend->begin_new_download_markup($form_state);
      if($form_state->getValue("") == "docx"){
        $text = $this->backend->get_markup();
        $word  = new html_to_word();
        $word->report_name = DRUPAL_ROOT."/modules/custom/washington_seminar_meeting_report.docx";
        $word->font_size = '11';
        \Drupal::logger("wtf")->notice("text: ".$text);
        $word->download_doc($text);}
      else{
          $data = $this->backend->get_array();
          $this->csv_Set_up($data);
      }

    }
    public function report_refresh(&$form, $form_state)
    {
        return $form['report_markup'];
    }
    public function csv_Set_up($data)
    {
       $year = date("Y");
        $filename = DRUPAL_ROOT."/modules/custom/".$year."_washington_seminar_rating_report.csv";
        $this->check_file_size($data, $filename, $file, $size);
        $this->file_download($file, $size, $filename);

    }
    public function check_file_size($data, $filename, &$file, &$size)
    {
        if (isset($data['0'])) {
            \Drupal::logger('nfb_washington_download')->notice("I am creating the csv ".$filename);
            $fp = fopen($filename, 'w');
            fputcsv($fp, array_keys($data['0']));
            foreach ($data AS $values) {
                \Drupal::logger('nfb_washington_download')->notice("value ".print_r($values, true));
                fputcsv($fp, $values);}
            fclose($fp);}
        ob_flush();
        $file = file_get_contents($filename);
        $size = @filesize($filename);
    }
    public function file_download($file, $size, $filename)
    {
        if (strlen($file) > 0) {
            ob_start();  // buffer all but headers
            ob_end_clean();  // headers get sent, erase all buffering and enable output
            header("Content-type: application/csv; charset=UTF-8");
            header("Content-length: " . $size);
            header('Pragma: public');
            header("Content-Description: PHP Generated Data");
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $file;
            unlink($filename);
            exit;}
    }
}