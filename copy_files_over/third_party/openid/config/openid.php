<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['openid_storepath'] = 'tmp';
$config['openid_request_to'] = 'login/finish';
$config['openid_user_url'] = "";

$config['openid_exclude_controllers'] = array(
  'login','logout'
);

$config['memcached_servers'] = array(
  'localhost' // replace with appropriate memcache servers
);