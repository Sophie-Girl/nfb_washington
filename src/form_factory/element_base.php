<?php
namespace Drupal\nfb_washington\form_factory;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class element_base
{
    use StringTranslationTrait; // use translation trait for form titles and drupal translate
    public $element_id; // form element id
    public $title; // element title
    public $type; // element type
    public $required; // is the field required. True or False
    public $callback; // ajax function
    public $wrapper; // ajax div wrapper id
    public $event; // event that triggers the ajax
    public $prefix; // div wrapper beginning
    public $suffix; // close div
    public $representative_data;
    public function get_element_id()
    {return $this->element_id;}
    public function get_element_title()
    {return$this->title;}
    public function get_element_type()
    {return $this->type;}
    public function get_element_required_status()
    {return $this->required;}
    public function  get_callback()
    {return $this->callback;}
    public function get_wrapper()
    {return $this->wrapper;}
    public function get_event()
    {return $this->event;}
    public function get_prefix()
    {return $this->prefix;}
    public function get_suffix()
    {return $this->suffix;}
}