<?php
namespace  Drupal\nfb_washington\form_factory\admin;
use Drupal\core\form\FormStateInterface;

class admin_members
{
    public function build_form_array(&$form, FormStateInterface $form_state)
    {
        $this->build_mode_select($form, $form_state);
        $this->build_markup($form, $form_state);
    }
    public function build_mode_select(&$form, FormStateInterface $form_state)
    {
        $form['members_mode']= array(
            '#type' => "select",
            "#title" => "Members of Congress Mode",
            '#required' => true,
            '#options' => array(
              "initial_upload" => "Import New Congress",
              "maintenance" => "Maintain Current Congressional Records"
            ),
            "#ajax" => array(
                "event" => "change",
                "callback" => '::markup_refresh',
                'wrapper' => "explain_markup"
            )
        );

    }
    public function build_markup(&$form, FormStateInterface $form_state)
    {
        $form['mode_explain'] = array(
          "#prefix" => "<div id = 'explain_markup'>",
          "#type" => "item",
          '#markup' => $this->markup_choice($form_state),
          "#suffix" => "</div>",
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => "Submit",);
    }
    public function markup_choice(FormStateInterface $form_state)
    {
        if($form_state->getValue("members_mode")== "")
        {$markup = "<p>Please select a mode</p>";}
        elseif($form_state->getValue("members_mode")== "initial_upload")
        {$markup = "<p><b>Import New Congress:</b> This will upload new members of congress, 
           while removing any members form the previous congress who resigned, did not 
           win reelection, or left office under other circumstances. Should be run after 
           the election of a new congress. It first removes records who are leavig 
           the previous congress, and then adds in the new ones</p>";}
        else {
            $markup = "<p><b>Maintain Current Congressional Records:</b> Checks the current congress for any members hwo have left 
            Congress. As well as important any people who have entered office due to special elections, 
            or other means.  Thjis should be run during a year when no general election is held the previous November</p>";
        }
        return $markup;
    }
}