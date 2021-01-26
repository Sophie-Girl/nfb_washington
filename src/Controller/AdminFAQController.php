<?php
Namespace Drupal\nfb_washington\Controller;
use Drupal\Core\Controller\ControllerBase;
class  AdminFAQController extends ControllerBase
{
    public function content()
    {
        $page = "
         <h2>Propublica API Information</h2>
         <p>This system relies on the Propublica Congressional API. Here are some answers for basic questiosn on the data source</p>
         <h3> How often does the data refresh?</h3>
         <p> Data is refreshed daily, while vote details updated every 30 minutes.</p>
         <h3> </h3>";
        return [
            '#type' => 'markup',
            '#markup' => $page,
        ];

    }
}