<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
  
  function __construct() {
    parent::__construct();
    $this->lang->load('openid', 'english');
    $this->load->library('openid');
    $this->load->helper('url');
  }
  
  function index() {
    $this->load->view('login/index');
  }
  
  function begin() {
    $this->config->load('openid');    
    $request_to = base_url()."index.php/login/finish/";
    $this->openid->set_request_to($request_to);
    $this->openid->set_ax(true,"http://axschema.org/contact/email");
    $this->openid->authenticate();
    exit();
  }
  
  function _set_message($msg, $val = '', $sub = '%s') {
    return str_replace($sub, $val, $this->lang->line($msg));
  }
  
  function finish() {
    $this->load->library('session');
    $this->config->load('openid');
    $request_to = base_url()."index.php/login/finish/";
    $this->openid->set_request_to($request_to);
    $response = $this->openid->getResponse();
    switch ($response->status) {
    case Auth_OpenID_CANCEL:
      $this->session->sess_destroy();
      exit("OpenID Authentication Cancelled");
      break;
    case Auth_OpenID_FAILURE:
      var_dump($response); 
      $this->session->destroy();
      exit("OpenID Authentication Failed");
      break;
    case Auth_OpenID_SUCCESS:
      $ax_resp = new Auth_OpenID_AX_FetchResponse();
      $ax = $ax_resp->fromSuccessResponse($response);
      $email = $ax->data["http://axschema.org/contact/email"][0];
      $user_info = array();
      $user_info['email'] = $email;
      $this->session->set_userdata('user_info',$user_info);
      header('Location:'.  base_url());
      break;
    default:
        $this->session->sess_destroy();
      	exit("Internal error");
      break;
    }  
  }
  
}
?>
