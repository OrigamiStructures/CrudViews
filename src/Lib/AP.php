<?php

use Cake\Core\InstanceConfigTrait;


/**
 * Description of AP
 *
 * @author dondrake
 */
class AP implements Iterator {
	
	use InstanceConfigTrait;

	protected $_defaultConfig;
	protected $_keys;
	
	protected $alias;
	protected $action;
	
	public function add($path) {
		
	}
	
	public function load($path = NULL) {
		if (is_null($path)) {
			$member = $this->_members;
		} else {
			$member = \Cake\Utility\Hash::get($this->$members, $path);
		}
	}
	
	public function keys() {
		return $this->_keys;
	}
	
	public function has($path) {
		
	}
	
	public function remove($path) {
		
	}
	
	public function current() {
		
	}

	public function key() {
		
	}

	public function next() {
		
	}

	public function rewind() {
		
	}

	public function valid() {
		
	}

}
