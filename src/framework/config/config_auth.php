<?php

$config['use_authentication']            = true;
$config['roles']                         = array (
  0 => 'logged_in',
);
$config['users']                         = array (
  'test_user' => 
  array (
    'role' => 'logged_in',
    'password' => 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',
  ),
  'test_user_second' => 
  array (
    'role' => 'unknown_role',
    'password' => '51abb9636078defbf888d8457a7c76f85c8f114c',
  ),
);
