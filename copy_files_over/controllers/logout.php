<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {
  
  function __construct() {
    parent::__construct();
    $this->load->helper('url');
  }
  
  function index() {
    $this->load->library('Session');    
    $this->session->destroy();
    header("Location: ".base_url()."login");
    exit();
  }
  
}