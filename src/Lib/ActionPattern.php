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
		'action' => NULL,
		'tools' => [],
	];
	
	/**
	 * All the configured aliases, their actions and tool sets
	 *
	 * @var array
	 */
	public $_tools;

	/**
	 * The currently targeted alias
	 *
	 * @var string
	 */
	protected $_alias = 'default';
	
	/**
	 * The currently targeted action
	 *
	 * @var string
	 */
	protected $_action;
	
	/**
	 * The labels => actions for the current alias/action
	 *
	 * @var array
	 */
	public $tools = [];
	
	/**
	 * The current Request object
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * Build the object
	 * 
	 * Set up with the provided configuration 
	 * then establish the requested target tool set or a default. 
	 * Default will be extracted from the Request (controller/action).
	 * 
	 * @param Request $request
	 * @param array $config
	 */
	public function __construct(Request $request, $config) {
		$this->request = $request;
		$this->config($config);
		$this->_action = !isset($config['action']) || is_null($config['action'])  ? $this->request->action : $config['action'];
		$this->_tools = $this->_config['tools'];
		$this->load();
	}
	
	/**
	 * Get all keys (aliases) or check existance of one
	 * 
	 * @param string $key
	 * @return mexed
	 */
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
	 * _alias and _action are always kept current. __construct sets them to 
	 * something meaningful also. So, calling with NULLs will get the current 
	 * values concatenated as a dot-path. The args can be set independently. 
	 * 
	 * Also, the alias is checked for existance and if missing is changed 
	 * to 'default'. This is to allow automatic operation in a 'plain crud' 
	 * environment without requiring special Action configurations. The 
	 * 'defaults' should 'just work'.
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
		
		if (!is_null($action)) {
//			debug(4);
//			$this->_action = $this->request->action;
//		} else {
//			debug(5);
			$this->_action = $action;
		}
		
//		osd("$this->_alias.$this->_action", 'aslias.action');
		return "$this->_alias.$this->_action";
	}

	/**
	 * Establish a tool set as the current tool set
	 * 
	 * @param string $alias 
	 * @param string $action
	 * @return array the tool set [label => action]
	 */
	public function load($alias = NULL, $action = NULL) {
		$path = $this->buildPath($alias, $action);
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
		
	/**
	 * Overwrite old or set new tool values
	 * 
	 * Will do Hash::remove(path) then Hash::insert(path, data)
	 * 
	 * @param string $path
	 * @param array $values
	 */
	public function set($path, $values = null) {
		$this->remove($path);
		$this->insert($path, $values);
	}
	
	/**
	 * Insert new action pattern according to Hash::insert rules
	 * 
	 * @param string $path
	 * @param array $values
	 * @return array
	 */
	public function insert($path, $values = null) {
		$this->_tools = Hash::insert($this->_tools, $path, $values);
		return $this->_tools;
	}
	
	/**
	 * Remove the data at the specified path from the tool set
	 * 
	 * @param string $path
	 * @return array
	 */
	public  function remove($path) {
		return Hash::remove($this->_tools, $path);
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
