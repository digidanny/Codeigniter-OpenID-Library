<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* OpenID Library
*
* @package    CodeIgniter
* @author     bardelot
* @see        http://cakebaker.42dh.com/2007/01/11/cakephp-and-openid/
*             & http://openidenabled.com/php-openid/
*/

class Openid{
  
  var $storePath = 'tmp';
  
  var $sreg_enable = false;
  var $sreg_required = null;
  var $sreg_optional = null;
  
  var $ax_enable = true;
  
  var $request_to;
  var $trust_root;
  
  function Openid() {
    $CI =& get_instance();    
    $CI->config->load('openid');
    // $this->storePath = $CI->config->item('openid_storepath');
    // solves the non authenticate problem...
    $this->storePath = BASEPATH."cache/openid/";
    #session_start();   
    $CI->load->library('Session'); 
    $this->_doIncludes();
    log_message('debug', "OpenID Class Initialized");
		$this->OpenIDConsumer = $this->getConsumer();
  }
  
  function _doIncludes() {
    set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
    require_once "Auth/OpenID/AX.php";
    require_once "Auth/OpenID/Consumer.php";
    require_once "Auth/OpenID/FileStore.php";
    require_once "Auth/OpenID/MemcachedStore.php";
    require_once "Auth/OpenID/SReg.php";
    require_once "Auth/OpenID/google_discovery.php";
  }
  
  function set_sreg($enable, $required = null, $optional = null) {
    $this->sreg_enable = $enable;
    $this->sreg_required = $required;
    $this->sreg_optional = $optional;
  }
  
  function set_ax($enable, $uri = null) {
    $this->ax_enable = $enable;
    $this->ax_uri = $uri;
  }
  
  function set_request_to($uri) {
    $this->request_to = $uri;
  }
  
  function _set_message($error, $msg, $val = '', $sub = '%s') {
    $CI =& get_instance();
    $CI->lang->load('openid', 'english');
    echo str_replace($sub, $val, $CI->lang->line($msg));
    
    if ($error) {
      exit;
    }
  }
  
  function authenticate() {
		$trust_root = base_url();
		$ci = get_instance();
    $authRequest = $this->OpenIDConsumer->begin($ci->config->item('openid_user_url'));
    
    // No auth request means we can't begin OpenID.
    if (!$authRequest) {
      $this->_set_message(true,'openid_auth_error');
    }
      
    if ($this->sreg_enable) {
      $sreg_request = Auth_OpenID_SRegRequest::build($this->sreg_required, $this->sreg_optional);
      if ($sreg_request) {
        $authRequest->addExtension($sreg_request);
      } else {
        $this->_set_message(true,'openid_sreg_failed');
      }
    }
    
    if ($this->ax_enable) {
      $ax_request = new Auth_OpenID_AX_FetchRequest();
      $ax_request->add(new Auth_OpenID_AX_AttrInfo($this->ax_uri,1,true,null));
      if ($ax_request) {
       $authRequest->addExtension($ax_request);
      } else {
        // error
      }
    }
    
    // if ($this->ext_args != null) {
    //   foreach ($this->ext_args as $extensionArgument) {
    //     if (count($extensionArgument) == 3) {
    //       $authRequest->addExtensionArg($extensionArgument[0], $extensionArgument[1], $extensionArgument[2]);
    //     }
    //   }
    // }
    
    // Redirect the user to the OpenID server for authentication.
    // Store the token for this authentication so we can verify the
    // response.

    // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
    // form to send a POST request to the server.
    if ($authRequest->shouldSendRedirect()) {
      $redirect_url = $authRequest->redirectURL($trust_root, $this->request_to);
      // $redirect_url = $authRequest->redirectURL($trust_root, site_url($this->uri->uri_string());
      
      // If the redirect URL can't be built, display an error message.
      
      if (Auth_OpenID::isFailure($redirect_url)) {
        $this->_set_message(true,'openid_redirect_failed', $redirect_url->message);
      } else {
        // Send redirect.
        header("Location: ".$redirect_url);
      }
    } else {
      // Generate form markup and render it.
      $form_id = 'openid_message';
      $form_html = $authRequest->htmlMarkup($trust_root, $this->request_to, false, array('id' => $form_id));
      
      // Display an error if the form markup couldn't be generated;
      // otherwise, render the HTML.
      if (Auth_OpenID::isFailure($form_html)) {
        $this->_set_message(true,'openid_redirect_failed', $form_html->message);
      } else {
        print $form_html;
      }
    }
  }
  
  function getStore() {
    // {{{ Preconditions
		// -- Make sure that the store path exists
		/*if (!file_exists($this->storePath) && !mkdir($this->storePath)) {
			print('Could not create the FileStore directory "'.$this->storePath.'". Please check the effective permissions.');
		}*/
		// -- Make sure it is readable and writable
		/*if ((!is_readable($this->storePath)) || (!is_writable($this->storePath))) {
			print('The FileStore directory "'.$this->storePath.'" exists, but is not writable or readable. Please check the effective permissions.');
		}*/
		// }}}
		// Create and return
		#return new Auth_OpenID_FileStore($this->storePath);
    $ci =& get_instance();
    $s = $ci->config->item('memcached_servers');
    $memcache = new Memcache;
    foreach($s as $k => $server) { @$memcache->addServer($server, 11211); }
		return new Auth_OpenID_MemcachedStore($memcache);
	}
  
  function getConsumer() {
    /*if (!file_exists($this->storePath) && !mkdir($this->storePath)) {
      $this->_set_message(true,'openid_storepath_failed', $this->storePath);
    }*/
		$consumer = new Auth_OpenID_Consumer($this->getStore());
		new GApps_OpenID_Discovery($consumer);
		return $consumer;
	}
  
  function getResponse() {
    $response = $this->OpenIDConsumer->complete($this->request_to);
    return $response;
  }
}
?>
