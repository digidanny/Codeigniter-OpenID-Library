<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|
| Make sure the following variables are set in ./application/config/config.php
|
|*/


/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the 'hooks' feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/

$config['enable_hooks'] = TRUE;

/*
| -------------------------------------------------------------------
|  Auto-load Config files
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['config'] = array('config1', 'config2');
|
| NOTE: This item is intended for use ONLY if you have created custom
| config files.  Otherwise, leave it blank.
|
*/

$autoload['config'] = array('openid');

/* End of file config.php */
/* Location: ./application/config/config.php */