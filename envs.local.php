<?php

$envs = array();

$envs['production'] = array(
  'https://wp.devraa.com',
);

$envs['local'] = array(
  'http://wp.test',
  'http://books.wp.test',
);

$envs['staging'] = array(
  'https://staging.wp.devraa.com',
);

return $envs;