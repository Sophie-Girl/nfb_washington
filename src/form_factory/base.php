<?php
namespace Drupal\nfb_washington\form_factory;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
class base
{
    use StringTranslationTrait; // use translation trait for form titles and drupal translate
    public $element_id; // form element id
    public $title; // element title
    public $type; // element type
    public function get_element_id()
    {return $this->element_id;}
    public function get_element_title()
    {return$this->title;}
    public function get_element_type()
    {return $this->type;}
}