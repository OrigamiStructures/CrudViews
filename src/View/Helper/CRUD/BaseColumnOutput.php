<?php
namespace CrudViews\View\Helper\CRUD;

use CrudViews\View\Helper\CRUD\ColumnOutputInterface;
use Cake\I18n\Number;
use CrudViews\View\Helper\CRUD\FieldSetups;
use \App\Lib\dmDebug;
use App\View\Helper\CrudViewResources\ColumnOutput;

/**
 * CrudFields base class to establish output for the possible field types
 * 
 * @author dondrake
 */
class BaseColumnOutput implements ColumnOutputInterface {
	
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

	public function __construct($helper) {
		$this->helper = $helper;
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
		return $this->$outputStrategy($field);
	}
	
	protected function integer($field, $options = []) { 
		return Number::format($this->helper->entity->$field, $this->helper->CrudData->attributes($field, 'number')); 
	}
	
	protected function biginteger($field, $options = []) {
		return Number::format($this->helper->entity->$field, $this->helper->CrudData->attributes($field, 'number'));
	}
	
	protected function decimal($field, $options = []) {
		return Number::format($this->helper->entity->$field, $this->helper->CrudData->attributes($field, 'number'));
	}
	
	protected function float($field, $options = []) {
		return Number::format($this->helper->entity->$field, $this->helper->CrudData->attributes($field, 'number'));
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

	protected function input($field, $options = []){
		return $this->helper->Form->input($field, $this->helper->CrudData->attributes($field, 'input'));
	}

	/**
	 * Does the entity the helper is holding have a uuid?
	 * 
	 * When building forms (and possibly other times) it is useful to uniquely 
	 * id the entity so that input IDs and the like can truely be unique.
	 * 
	 * @return boolean
	 */
	public function hasUuid() {
		return !is_null($this->helper->entity->_uuid);
	}

}
