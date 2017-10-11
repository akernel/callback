<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once __DIR__.'/base.php';

class CBElementLinkBuy extends CBElement
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$output= '<div style="float: left; margin: 5px 5px 5px 0; width: 250px;">
		<a href="http://akernel.ru/buy-callback/?domain=' .preg_replace('/^www\./si', '', JFactory::getURI()->getHost()). '" target="_blank">'.JText::_('MOD_CALLBACK_LINK_BUY1').'</a></div>';
		return $output;
	}
}


class JFormFieldLinkBuy extends CBElementLinkBuy
{
    var $type = 'linkbuy';
}

class JElementLinkBuy extends CBElementLinkBuy
{
    var $_name = 'linkbuy';
}
?>