<?php

namespace CrudViews\Lib;

/**
 * Description of Uuid
 *
 * @author jasont
 */
class Uuid {
	
	protected $_uuid = NULL;
	
	public function __construct() {
		$this->_uuid = uniqid();
	}
	
	public function attr($attrName, $prefix = NULL) {
		$full_prefix = is_null($prefix) ? '' : $prefix . '-';
		return "{$attrName}=\"{$full_prefix}{$this->_uuid}\"";
	}
	
	public function uuid($prefix = NULL) {
		$full_uuid = is_null($prefix) ? $this->_uuid : "{$prefix}-{$this->_uuid}";
		return $full_uuid;
	}
}
