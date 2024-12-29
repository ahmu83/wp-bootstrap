<?php

$end_includes = array(
  'inc/end-constants.php',
  'inc/class-inits.php',
);

foreach ($end_includes as $include) {

  wpb_inc($include);

}

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M' );
@ini_set( 'max_execution_time', '300' );

