<?php
// Защита от прямого доступа к файлу
defined('_JEXEC') or die('Restricted access');
  
// Подключение требуемых файлов
jimport('joomla.form.formfield');

/**
 * Создаем класс. Fieldname - имя типа
 */
class JFormFieldCallbackFields extends JFormField
{
    /**
     * @var $type    Имя типа
     */
    protected $type = 'callbackfields';
    /**
     * Метод, определяющий что будет выводить параметр
     *
     * @return    Результат вывода типа
     */
    protected function getInput()
    {
        $this->_params = $this->form->getData()->get('params');
        // Start capturing output into a buffer
		ob_start();

		// Include the requested template filename in the local scope
		// (this will execute the view logic).
		include __DIR__.'/callbackfields-input.php';

		// Done with the requested template; get the buffer and
		// clear it.
		$this->input = ob_get_contents();
		ob_end_clean();
        
        return $this->input;
    }
}
