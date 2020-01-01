<?php
Use Drupal\nfb_washington\archive_nfb\representative_data;
$query = new representative_data();
$state = "MD";
$forms_state = '';
$query->build_state_array($forms_state);
print_r($query->get_rep_result());

