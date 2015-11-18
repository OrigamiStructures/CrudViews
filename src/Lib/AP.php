<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AP
 *
 * @author dondrake
 */
class AP {
	
	protected $_members;
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
	
	public function label() {
		
	}
	
	public function action() {
		
	}
}
