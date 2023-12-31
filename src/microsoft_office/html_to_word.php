<?php
Namespace Drupal\nfb_washington\microsoft_office;
class html_to_word
{

    public $font_size;
    public function get_font_size()
    {return $this->font_size;}
    public $report_name;
    public function get_report_name(){
    return $this->report_name;}
    public function download_doc($text)
    {
        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpword->addSection();
        $text_xplode = explode("\n", $text);
        foreach($text_xplode as $line){
            if($line == "-"){
                $line = " ";
     /*   $section->addText($line,
            array('name' => 'Tahoma', 'size' => $this->get_font_size())); */
        $section->addPageBreak();}
            elseif(strpos(" ".$line, "-ns-") > 0)
            {
                $new_line = str_replace("-ns-", "", $line);
                $section->addText($new_line,
                    array('name' => 'Arial', 'size' => '20', 'underline' => 'single'));

            }
        else{
            $section->addText($line,
                array('name' => 'Arial', 'size' => "16"));
           // $section->addTextBreak();
    }
        }
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $objWriter->save($this->get_report_name());
        header("Content-Disposition: attachment; filename='".$this->get_report_name()."'");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$this->get_report_name());
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->get_report_name()));
        flush();
        readfile($this->get_report_name());
        unlink($this->get_report_name());
    }
    public function add_page_break($text)
    {


    }


}