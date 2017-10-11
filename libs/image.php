<?php
/*
 * @package Joomla 1.5/1.6/1.7/2.5/3.0
 * @copyright Copyright (C) 2012. All rights reserved.
 *
 * @Module Callback aKernel
 * @copyright Copyright (C) A.Kernel www.akernel.ru
 */

// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', preg_replace('|\Smodules\Smod_.*?\Simage.php|i', '', __FILE__));

define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

$mainframe =& JFactory::getApplication('site');

$mainframe->initialise();

require(dirname(__FILE__).'/kcaptcha/kcaptcha.php');
@session_start();
$captcha = new KCAPTCHA();
$_SESSION['callback-captcha-code'] = $captcha->getKeyString();
exit;
?>