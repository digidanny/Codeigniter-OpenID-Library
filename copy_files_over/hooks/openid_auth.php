<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function openid_auth() {

  $ci = get_instance();
  $ci->load->config('openid');
  # if controller is in array, don't require openid auth
	if (!in_array($ci->router->class,$ci->config->item('openid_exclude_controllers'))) {

	  # require open_id 
    
    $user_info = $ci->session->userdata('user_info');
    
    if (!$user_info) {
      header("Location: ".base_url()."index.php/login");
      exit();
    }
    
  }
  
}