<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once __DIR__.'/base.php';

class CBElementTemplate extends CBElement
{
	function fetchElement($name, $value, &$node, $control_name)
	{
        jimport('joomla.filesystem.folder');
        $moduleName = 'mod_callback';
        $moduleTemplatesPath = JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR.'tmpl';
        $moduleTemplatesFolders = JFolder::folders($moduleTemplatesPath);
        
        $db = JFactory::getDBO();
        if ((int)JVERSION != 1)
        {
            $query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
        }
        else
        {
            $query = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0";
        }
        $db->setQuery($query);
        $defaultemplate = $db->loadResult();
        $templatePath = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$defaultemplate.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.$moduleName;

        if (JFolder::exists($templatePath))
        {
            $templateFolders = JFolder::folders($templatePath);
            $folders = @array_merge($templateFolders, $moduleTemplatesFolders);
            $folders = @array_unique($folders);
        }
        else
        {
            $folders = $moduleTemplatesFolders;
        }

        $exclude = 'default';
        $options = array();

        foreach ($folders as $folder)
        {
            if (preg_match(chr(1).$exclude.chr(1), $folder))
            {
                continue;
            }
            $options[] = JHTML::_('select.option', $folder, $folder);
        }

        array_unshift($options, JHTML::_('select.option', 'default', '-- '.JText::_('MOD_CALLBACK_USE_DEFAULT').' --'));

        if ((int)JVERSION != 1)
        {
            $fieldName = $name;
        }
        else
        {
            $fieldName = $control_name.'['.$name.']';
        }

        return JHTML::_('select.genericlist', $options, $fieldName, 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
}


class JFormFieldTemplate extends CBElementTemplate
{
    var $type = 'template';
}

class JElementTemplate extends CBElementTemplate
{
    var $_name = 'template';
}
?>