<?php
// no direct access
defined('_JEXEC') or die ;

if ((int)JVERSION == 1) {
    jimport('joomla.html.parameter.element');
    class CBElement extends JElement
    {
    }

} else {
	
    jimport('joomla.form.formfield');
    class CBElement extends JFormField
    {

        function getInput()
        {
            return $this->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
        }

        function getLabel()
        {
            if (method_exists($this, 'fetchTooltip'))
            {
                return $this->fetchTooltip($this->element['label'], $this->description, $this->element, $this->options['control'], $this->element['name'] = '');
            }
            else
            {
                return parent::getLabel();
            }

        }

        function render($layoutId = null, $data = array())
        {
            return $this->getInput();
        }

    }

}
