<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once __DIR__.'/base.php';

class CBElementSeparator extends CBElement
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$output= '<hr />';
		return $output;
	}
}


class JFormFieldSeparator extends CBElementSeparator
{
    var $type = 'linkbuy';
}

class JElementSeparator extends CBElementSeparator
{
    var $_name = 'linkbuy';
}
?>