<?php
Namespace Drupal\nfb_washington\form_factory;
class markup_elements extends date_elements
{
    public $markup;
    public function get_markup()
    {return $this->markup;}
    public function build_static_markup(&$form)
    {
        $form[$this->get_element_id()] = array(
            '#type' => $this->get_element_type(),
            '#markup' => $this->get_markup(),);
    }
}