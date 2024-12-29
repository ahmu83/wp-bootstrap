<?php

$envs = array();

$envs['production'] = array(
  'https://website.com',
);

$envs['local'] = array(
  'http://website.test',
  'http://books.website.test',
);

$envs['staging'] = array(
  'https://staging.website.com',
);

return $envs;