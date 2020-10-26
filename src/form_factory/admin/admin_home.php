<?php
namespace  Drupal\nfb_washington\form_factory\admin;
class admin_home
{
    public function configuration_markup(&$form, $form_state)
    {
        $form['config_markup'] = array(
          '#type' => "item",
          '#markup' => "<h3>Configuration</h3>
<p class='right-side'>Set API keys, set congress number, and other module settings <span ><a href='/nfb_washington/admin/configuration' class='button-2'>&nbsp;&nbsp;&nbsp;Configuration&nbsp;&nbsp;&nbsp;</a> </span></p>"
        );
    }
    public function members_markup(&$form, $form_state)
    {
        $form['members_markup'] = array(
            '#type' => "item",
            '#markup' =>  "<h3>Members of Congress</h3>
<p class='right-side'> Import a new congress, run mtnence on current roster of Members of Congress <span><a href='/nfb_washington/admin/members' class='button-1'>&nbsp;&nbsp;&nbsp;Members&nbsp;&nbsp;&nbsp;</a></span></p>"
        );
    }
    public function committee_markup(&$form, $form_state)
    {
        $form['committee_markup'] = array(
            '#type' => "item",
            '#markup' => "<h3>Committees</h3>
<p class='right-side'> Import committewes, find members, update membership. <span><a href='/nfb_washington/admin/committee' class='button-2'>&nbsp;&nbsp;&nbsp;Committee&nbsp;&nbsp;&nbsp;</a> </span></p>");
    }
    public function issues_markup(&$form, $form_state)
    {
        $form['issue_markup'] = array(
            '#type' => "item",
            '#markup' => "<h3>Issues</h3>
<p class='right-side'> Create and edit issues, link issues to past issues. <span><a href='/nfb_washington/admin/issue' class='button-3'>&nbsp;&nbsp;&nbsp;Issues&nbsp;&nbsp;&nbsp;</a> </span></p>");
    }
    public function note_markup(&$form, $form_state)
    {
        $form['notes_markup'] = array(
            '#type' => "item",
            '#markup' => "<h3>Notes</h3>
<p class='right-side'> Create and link notes. <span><a href='/nfb_washington/admin/notes' class='button-1'>&nbsp;&nbsp;&nbsp;Notes&nbsp;&nbsp;&nbsp;</a> </span></p>");
    }
    public function build_form_markups(&$form, $form_state)
    {
        $this->members_markup($form, $form_state);
        $this->committee_markup($form, $form_state);
        $this->issues_markup($form, $form_state);
        $this->note_markup($form,$form_state);
        $this->configuration_markup($form, $form_state);
    }
}