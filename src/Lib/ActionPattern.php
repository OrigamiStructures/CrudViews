<?php
namespace CrudViews\Lib;

use Cake\Core\InstanceConfigTrait;
use Cake\Network\Request;
use Cake\Collection\Collection as Coll;
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
		debug($this->tools);
		return $this->tools;
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
