<?php
/**
 * A.Kernel Callback! Module Callback
 * 
 * @package    Joomla 1.5/1.6/1.7/2.5/3.0/3.6
 * @license    http://www.akernel.ru/license/callback.html
 * @copyright  Copyright (C) 2017. All rights reserved.
 * 
 * @Module     Callback aKernel
 * @copyright  Copyright (C) A.Kernel www.akernel.ru
 */
defined('_JEXEC') or die('Direct Access to '.basename(__FILE__).' is not allowed.');

// Include the archive functions only once
require_once dirname(__FILE__) . '/helper.php';
$helper = new modCallbackHelper();

$doc = JFactory::getDocument();
$helper->instance($module,$params);
$show_captcha = $params->get('show_captcha', 1);
$type_captcha = $params->get('type_captcha', 0);
$return = modCallbackHelper::getReturnUrl();
$getTemplate = $params->get('tmpl', 'default');
$fields = array_values($params->get('fields',array()));

// Получаем суффикс класса модуля из параметров и экранируем его
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

if($fields){
    require JModuleHelper::getLayoutPath('mod_callback', $getTemplate.DIRECTORY_SEPARATOR.'default');
}