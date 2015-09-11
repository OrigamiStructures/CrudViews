<?php

namespace CrudViews\Error;

use Cake\Error\ExceptionRenderer as BaseExceptionRenderer;

/**
 * Description of ExceptionRenderer
 *
 * @author dondrake
 */
class ExceptionRenderer extends BaseExceptionRenderer{
	
	/**
	 * The exceptions belonging to the plugin
	 *
	 * @var array
	 */
	protected $_exceptions = [
		'missingFieldSetupFile', 'missingFieldSetupException', 
		'missingActionConfigException', 'missingConfigFileException'
	];

	/**
	 * Make CrudView exception rendering templates work
	 * 
	 * @param \CrudViews\Error\Exception $exception
	 * @param type $method
	 * @param type $code
	 * @return string
	 */
	protected function _template(Exception $exception, $method, $code)
    {
		debug('template');
		parent::_template($exception, $method, $code);
		if (in_array($this->template, $this->_exceptions)) {
			$this->template = 'CrudViews.' . $this->template;
		}
		return $this->template;
	}
	
}
