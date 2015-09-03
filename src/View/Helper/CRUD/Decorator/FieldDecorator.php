<?php
namespace CrudViews\View\Helper\CRUD\Decorator;
use CrudViews\View\Helper\CRUD\FieldOutputInterface;
/**
 * FieldDecorator is the base class for Decorators
 * 
 * Establishes basic functionality, properties, and 
 * the default behaviors for the Inferface method
 *
 * @author dondrake
 */
class FieldDecorator implements FieldOutputInterface {
	
	/**
	 * The decorated object
	 *
	 * @var object
	 */
	public $base;
	
	/**
	 * The Helper class currently managing output
	 * 
	 * The helper also contains an entity property containing the 
	 * entity currently being processed
	 *
	 * @var CrudHelper
	 */
	public $helper;
	
	/**
	 * Decorate an object
	 * 
	 * The decorated objet gets stored in $this->base. 
	 * Decorated objects always carry a helper and that gets 
	 * stored in $this->helper.
	 * 
	 * @param FieldOutputInterface $object
	 */
	public function __construct(FieldOutputInterface $object) {
		$this->base = $object;
		if (!$this->helper) {
			$this->helper = $this->base->helper;
		}
	}
	
	/**
	 * Call the next output in the chain
	 * 
	 * DOES THIS TELL THE WHOLE STORY?
	 * 
	 * @param string $field The current column name being output
	 * @param array $options options being sent to the next object in the sequence
	 * @return string The product of the operation
	 */
	public function output($field, $options = array()) {
		return $this->base->output($field, $options);
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
