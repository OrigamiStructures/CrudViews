<?php
namespace CrudViews\Lib;

use Cake\Core\InstanceConfigTrait;
use Cake\Network\Request;


/**
 * Description of AP
 *
 * @author dondrake
 */
class ActionPattern {
	
	use InstanceConfigTrait;

	protected $_defaultConfig = [
		'alias' => 'default',
		'action' => '',
		'tools' => [],
	];
	protected $_keys;
	protected $_tools;


	protected $_alias;
	protected $_action;
	
	public $tools = [];
	protected $request;


	public function __construct(Request $request, $config) {
		$this->request = $request;
		$this->config($config);
		
		$this->_alias = empty($this->_config['alias']) ? strtolower($this->request->controller) : $this->_config['alias'];
		$this->_action = empty($this->_config['action']) ? $this->request->action : $this->_config['action'];
		$this->_tools = $this->_config['tools'];
		
		$this->load($this->currentPath());
	}
	
	/**
	 * Get the dot notation of the alias and action 
	 * 
	 * @return type string
	 */
	public function currentPath() {
		return "$this->_alias.$this->_action";
	}

	/**
	 * Establish a tool set as the current tool set
	 * 
	 * @param type $path
	 */
	public function load($path = NULL) {
		if (is_null($path)) {
			$path = $this->currentPath();
		}
		$this->tools = \Cake\Utility\Hash::get($this->_tools, $path);
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
	
	public function __debugInfo() {
		return [
			'[protected] _action' => $this->_action,
			'[protected] _alias' => $this->_alias,
			'[public] tools' => $this->tools,
			'[protected] _keys' => $this->_keys,
			'[protected] _tools' => $this->_tools,
			'[protected] request->params' => $this->request->params,
			'[protected] _config' => $this->_config, 
			'[protected] _defaultConfig' => $this->_defaultConfig, 
			];
	}

}
