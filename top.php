<?php

if (WPB_ACTIVE !== true) {

  return;

}


$top_includes = array(
  'vendor/autoload.php',
  'inc/top-constants.php',
  'inc/functions.php',
);

foreach ($top_includes as $include) {

  wpb_inc($include);

}

// set_time_limit(300);

