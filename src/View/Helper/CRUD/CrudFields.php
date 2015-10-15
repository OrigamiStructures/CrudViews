<?php
namespace CrudViews\View\Helper\CRUD;

use CrudViews\View\Helper\CRUD\FieldOutputInterface;
use Cake\I18n\Number;
use CrudViews\View\Helper\CRUD\FieldSetups;
use \App\Lib\dmDebug;

/**
 * CrudFields base class to establish output for the possible field types
 * 
 * @author dondrake
 */
class CrudFields implements FieldOutputInterface {
	
	protected $column_types = [
			'date', 
			'time', 
			'datetime', 
			'timestamp', 
			'boolean',
			'uuid',
			'string',
			'binary',
		
			'biginteger',
			'integer',
			'float',
			'decimal'
	];
	
	/**
	 * The containing helper class and all its properties
	 * 
	 * This is the way of getting all the data knowledge donwn 
	 * into the hole we're digging.
	 *
	 * @var CrudHelper
	 */
	public $helper;
	
	protected $Text;

	protected $Number;

	public function __construct($helper) {
		$this->helper = $helper;
		$this->Number = new Number;
//		if (get_class($config[0]) === 'CrudHelper' || is_subclass_of($config[0], 'CrudHelper')) {
//			$this->_helper = $config[0];
//		}
//		parent::__construct();
	}

	/**
	 * The main call point for output of a field
	 * 
	 * This gives the sub-classes a chance to decide if any special 
	 * process should take place before calling for final output
	 * 
	 * @param string $field
	 * @return string
	 */
	public function output($field, $options = []) {
		$outputStrategy = $this->helper->columnType($field);
		if (method_exists($this, $outputStrategy)) {
			return $this->$outputStrategy($field, $options);
		} else {
			// ################################
			// This needs an Exceptions
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			if (!isset($this->helper->ColumnTypeHelper)) {
				$this->helper->loadCustomSetups();
			}
			return $this->helper->ColumnTypeHelper->$outputStrategy($field, $options);
		}
	}
	
	protected function integer($field, $options = []) { 
		return $this->Number->format($this->helper->entity->$field); 
	}
	
	protected function biginteger($field, $options = []) {
		return $this->Number->format($this->helper->entity->$field);
	}
	
	protected function decimal($field, $options = []) {
		return $this->Number->format($this->helper->entity->$field);
	}
	
	protected function float($field, $options = []) {
		return $this->Number->format($this->helper->entity->$field);
	}
	
	protected function date($field, $options = []) {
		return h($this->helper->entity->$field);
	}
	
	protected function time($field, $options = []) {
		return h($this->helper->entity->$field);
	}
 
	protected function datetime($field, $options = []) {
		return h($this->helper->entity->$field);
	}
 
	protected function timestamp($field, $options = []) {
		return h($this->helper->entity->$field);
	}
 
	protected function boolean($field, $options = []) {
		return h($this->helper->entity->$field  ? __('Yes') : __('No'));
	}

	protected function uuid($field, $options = []) {
		return h($this->helper->entity->$field);
	}

	protected function string($field, $options = []) {
		return h($this->helper->entity->$field);
	}

	protected function binary($field, $options = []) {
//		return h($this->helper->entity->$field);
		return h('Info about the binary blob');
	}

	protected function text($field, $options = []) {
//		if (!$Text) {
//			$this->Text = $this->helper->_View->loadHelper('Text');
//		}
		return $this->helper->Text->autoParagraph(h($this->helper->entity->$field));
	}

	protected function input($field, $options){
		return $this->helper->Form->input($field, $options);
	}
	
}
