<?php
namespace CrudViews\Lib;

use Cake\Core\InstanceConfigTrait;
use Cake\Network\Request;
use Cake\Utility\Hash;


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
	protected $_tools;


	protected $_alias = 'default';
	protected $_action;
	
	public $tools = [];
	protected $request;


	public function __construct(Request $request, $config) {
		$this->request = $request;
		$this->config($config);
		
//		$this->_alias = empty($this->_config['alias']) ? strtolower($this->request->controller) : $this->_config['alias'];
//		$this->_action = empty($this->_config['action']) ? $this->request->action : $this->_config['action'];
		$this->_tools = $this->_config['tools'];
		
		$this->load($this->buildPath());
//		debug($this);
	}
	
	public function keys($key = NULL) {
		if (is_null($key)) {
			return array_keys($this->_tools);
		} else {
			return array_key_exists($key, $this->_tools);
		}
	}
	
	/**
	 * Get the dot notation of the alias and action or a default path
	 * 
	 * @return type string
	 */
	public function buildPath($alias = NULL, $action = NULL) {
		
		if (is_null($alias)) {
//			debug(1);
			$this->_alias = $this->_alias;
		} else {
//			debug(2);
			$this->alias = $alias;
		}
		
		if (!$this->keys($this->_alias)) {
//			debug(3);
			$this->_alias = 'default';
		}
		
		if (is_null($action)) {
//			debug(4);
			$this->_action = $this->request->action;
		} else {
//			debug(5);
			$this->_action = $action;
		}
		
		return "$this->_alias.$this->_action";
	}

	/**
	 * Establish a tool set as the current tool set
	 * 
	 * @param type $path
	 */
	public function load($alias = NULL, $action = NULL) {
			$path = $this->buildPath();
//		if (!stristr($path, '.')) {
//			throw new \BadMethodCallException('Load path must be in the form alias.action.');
//		}
		$t = Hash::get($this->_tools, $path);
		$t = is_null($t) ? [] : $t;
		$this->tools = [];
		foreach ($t as $key => $tool) {
			if (is_int($key)) {
				$this->tools[ucfirst($tool)] = $tool;
			} else {
				$this->tools[ucfirst($key)] = $tool;
			}
		}
		return $this->tools;
	}
		
	public function __debugInfo() {
		return [
			'[protected] _action' => $this->_action,
			'[protected] _alias' => $this->_alias,
			'[public] tools' => $this->tools,
			'[protected] _tools' => $this->_tools,
			'[protected] request->params' => $this->request->params,
			'[protected] _config' => $this->_config, 
			'[protected] _defaultConfig' => $this->_defaultConfig, 
			];
	}

}
