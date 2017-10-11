<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php if($params->get('mod_type')=='popup' && !JRequest::getVar('callback-result')): ?>
<div id="jCallBack_fade" class="jCallBack_fade"></div>
<div id="jCallBackResult"><div><span></span></div></div>
<?php
JRequest::setVar('callback-result',1);
endif;
require JModuleHelper::getLayoutPath('mod_callback', $getTemplate.DIRECTORY_SEPARATOR.'default_' . $params->get('mod_type','popup'));
?>