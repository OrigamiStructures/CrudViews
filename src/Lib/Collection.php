<?php
namespace CrudViews\Lib;

/**
 * Collection - A super simple keyed collection manager
 * 
 * Adds, removes and retrieves members.
 * Also verifies existance (by key)
 *
 * @author dondrake
 */
class Collection {
	
	/**
	 * The members
	 *
	 * @var array
	 */
	protected $_members = [];

	/**
	 * The keys
	 *
	 * @var array
	 */
	protected $_keys = [];

	/**
	 * Add a new member or replace one if the key already exists
	 * 
	 * @param string $key
	 * @param mixed $member
	 */
	public function add($key, $member) {
		$this->_members[$key] = $member;
		$this->keys();
	}
	
	/**
	 * Remove a member
	 * 
	 * @param string $key
	 */
	public function remove($key) {
		if ($this->has($key)) {
			unset($this->_members[$key]);
			$this->keys();
		}		
	}
	
	/**
	 * Get a member from the collection
	 * 
	 * @param string $key
	 * @return mixed|boolean False if the member didn't exist
	 */
	public function load($key) {
		if ($this->has($key)) {
			return $this->_members[$key];
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Determine if a key exists
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return in_array($key, $this->_keys);
	}
	
	/**
	 * Get the array of existing keys
	 * 
	 * @return array
	 */
	public function keys() {
		$this->_keys = array_keys($this->_members);
		return $this->_keys;
	}
}
