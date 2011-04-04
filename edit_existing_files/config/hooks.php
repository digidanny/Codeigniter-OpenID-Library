<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|
| Make sure the following value is defined in ./application/config/hooks.php
|
*/

$hook['post_controller_constructor'][] = array(
  'function' => 'openid_auth',
  'filename' => 'openid_auth.php',
  'filepath' => 'hooks'
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */