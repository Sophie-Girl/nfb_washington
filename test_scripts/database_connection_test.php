<?php
Use Drupal\nfb_washington\archive_nfb\representative_data;
$query = new representative_data();
$state = "MD";
$query->get_house_rep_for_state($state, $result);
$query->find_meeting($result, $array);
