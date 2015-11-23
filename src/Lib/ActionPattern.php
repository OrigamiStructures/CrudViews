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
	
	/**
	 * All the configured aliases, their actions and tool sets
	 *
	 * @var array
	 */
	protected $_tools;

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
		$this->_tools = $this->_config['tools'];
		$this->load($this->buildPath());
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
	 * @return type string
	 */
	public function buildPath($alias = NULL, $action = NULL) {
		
		if (is_null($alias)) {
			$this->_alias = $this->_alias;
		} else {
			$this->alias = $alias;
		}
		
		if (!$this->keys($this->_alias)) {
			$this->_alias = 'default';
		}
		
		if (is_null($action)) {
			$this->_action = $this->request->action;
		} else {
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

	/**
	 * Set or reset some level of the master _tools array
	 * 
	 * $path = array to merge new alias directly into master. 
	 * $path = 'alias.action' + data array as an alternative
	 * $path = 'alias' + data array as a 3rd alternative
	 * 
	 * @param array|string $path
	 * @param array $data
	 * @param boolean $replace
	 */
	public function add($path, $data = FALSE, $replace = FALSE) {
		if (is_array($path)) {
			$this->addModels($path, $data); // which are actually $data, $replace in this case
			
		} elseif (is_string($path)) {
			$levels = explode('.', $path);
			switch (count($levels)) {
				case 1: // alias level stable ->add('Users', [])
					$this->addViews($data, $replace);
					break;
				case 2: // view level stable ->add('Users.index', [])
					$this->addTools($data, $levels[1], $replace);
					break;
			}
		}
	}
	
	/**
	 * Add or overwrite one or more action sets in the collection
	 * 
	 * This will effect an alias level. All the current settings for any 
	 * referenced alias will be removed and replaced by the new settings.
	 * 
	 * <pre>
	 * [
	 *	'alias' => [
	 *		'view' => ['action', ['label' => 'action'], 'action'],
	 *		'view' => ['action']
	 *	],
	 *	'more-as-desired' => ['view' => ['action']]
	 * ]
	 * </pre>
	 * 
	 * @param array $aliasSettings
	 */
	protected function addModels($aliasSettings, $replace = FALSE) {

		foreach ($aliasSettings as $alias => $viewSettings) {
			if ($replace) {
				
			} else {
				
			}
			$this->addViews($viewSettings, $replace);
		}
	}
	
	protected function addViews($viewSettings, $replace = FALSE) {
		foreach ($viewSettings as $view => $toolSettings) {
			if ($replace) {
				
			} else {
				
			}
			$this->addTools($toolSettings, $view, $replace);
		}
	}
	
	protected function addTools($toolSettings, $view, $replace = FALSE) {
		foreach ($toolSettings as $action) {
			
		}
		if ($replace) {
			
		} else {
			
		}
	}
	
}
