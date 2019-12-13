<?php
Namespace Drupal\nfb_washington\form_factory;

class date_elements extends select_elements
{
    public $default_value;
    public $format;
    public $date_date_format;
    public $date_year_range;
    public $date_date_element;
    public $date_time_element;
    public $date_time_format;
    public function get_default_value()
    {return $this->default_value;}
    public function get_format()
    {return $this->format;}
    public function get_date_date_format()
    {return $this->date_date_format;}
    public function  get_year_range()
    {return $this->date_year_range;}
    public function get_date_date_element()
    {return $this->date_date_element;}
    public function get_date_time_element()
    {return $this->date_time_element;}
    public function get_date_time_format()
    {return $this->date_time_format;}

    public function day_picker(&$form)
    {
        $form[$this->get_element_id()] = array(
            '#type'  => $this->get_element_type(),
            '#title' => $this->t($this->get_element_title()),
            '#format' => $this->get_format(),
            '#required' => $this->get_element_required_status(),
            '#default_value' => $this->get_default_value(),
            '#date_year_range' => $this->get_year_range(),
            '#date_date_form' => $this->get_date_date_format(),
            '#min' => $this->get_element_min(),
            '#max' => $this->get_element_max(),
        );
    }
    public function meeting_day_element(&$form)
    {   $this->element_id = 'meeting_day'; $this->title = "Meeting day";
        $this->format = 'm/d/Y'; $this->date_date_format = 'm/d/Y';
        $this->required = True; $this->default_value = '2/1/2020';
        $this->min = '2/1/2020'; $this->max = '2/28/2020';
        $this->date_year_range = '-0: +0'; $this->type = 'date';
        $this->day_picker($form);}
    public function build_time_elements(&$form)
    {
        $form[$this->get_element_id()] = array(
           '#type' => $this->get_element_type(),
           '#title' => $this->t($this->get_element_title()),
           '#size' => $this->get_element_size(),
           '#date_date_element' => $this->get_date_date_element(),
           '#date_time_element' => $this->get_date_time_element(),
           '#date_time_format' => $this->get_date_time_format(),
        );
    }
    public function meeting_time_element(&$form)
    {
        $this->element_id = 'meeting_time'; $this->type = 'datetime';
        $this->size = '20'; $this->date_date_element = 'none'; // hope this fixes this stupid module 
        $this->date_time_element = 'time'; $this->date_time_format = 'H:i A';
        $this->default_value = '05:00'; $this->build_time_elements($form);
    }

}
