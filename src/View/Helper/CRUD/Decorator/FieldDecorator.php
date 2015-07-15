<?php
namespace CrudViews\View\Helper\CRUD\Decorator;
use CrudViews\View\Helper\CRUD\FieldOutputInterface;
/**
 * FieldOutputDecorator
 *
 * @author dondrake
 */
class FieldDecorator implements FieldOutputInterface {
	
	public $base;
	
	public $helper;
	
	public function __construct($object) {
		$this->base = $object;
		if (!$this->helper) {
			$this->helper = $this->base->helper;
		}
	}
	
	public function output($field, $options = array()) {
		return $this->base->output($field, $options);
	}
	
	public function hasUuid() {
		return !is_null($this->helper->entity->_uuid);
	}

}
