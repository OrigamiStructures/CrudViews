<?php
namespace CrudViews\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use CrudViews\View\Helper\CRUD\ToolPackage;
use CrudViews\Lib\Collection;
use Cake\Utility\Inflector;
use CrudViews\Lib\NameConventions;
use CrudViews\Lib\CrudConfig;
use CrudViews\Template\CRUD\Exception\MissingFieldSetupFileException;
use \CrudViews\Template\CRUD\Exception\MissingFieldSetupException;
use Cake\Core\Exception\Exception;
use \App\Lib\dmDebug;
use App\View\Helper\CrudViewResources\DecorationFactory;

//Here's the location of your custom FieldSetups
use App\View\Helper\CrudViewResources\FieldSetups;

class CrudHelper extends Helper
{
	
	public $helpers = ['Html', 'Form', 'Text', 'CrudViews.RecordAction', 'CrudViews.ModelAction', 'Paginator'];
	
	use CrudConfig;

	public $ModelActions;
	public $AssociationActions;
	public $RecordActions;

	/**
	 * The default (assumed) model alias (derived from the current controller)
	 *
	 * @var string
	 */
	protected $_defaultAlias;
	
	protected $currentStrategy;
	
	/**
	 * The current crud data object
	 *
	 * @var CrudData object
	 */
	public $CrudData;
	
	public $ColumnTypeHelper;
	
	public $DecorationSetups;
	
	/**
	 * The current entity
	 *
	 * @var Entity
	 */
	public $entity;
	
	public $ToolParser;
	
	protected $_aliasStack = [];


	/**
	 * Make the helper, possibly configuring with CrudData objects
	 * 
	 * @param \Cake\View\View $View
	 * @param array $config An array of CrudData objects
	 */
	public function __construct(View $View, array $config = array()) {
		parent::__construct($View, $config);
		
		$config += ['_CrudData' => [], 'actions' =>[]];
		$this->_defaultAlias = new NameConventions(Inflector::pluralize(Inflector::classify($this->request->controller)));
		$this->_CrudData = $config['_CrudData'];
		$this->DecorationSetups = new DecorationFactory($this);
//		$this->_Field = new Collection();
				
		foreach ($config['actions'] as $name => $pattern) {
			$this->{$name} = $pattern;
		}   
		$this->useCrudData($this->_defaultAlias->name);
    }
	
	/**
	 * Pass through calls to CrudData
	 * 
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public function __call($method, $params = []) {
		if (method_exists($this->CrudData, $method)) {
			switch (count($params)) {
				case 0:
					return $this->CrudData->$method();
					break;
				case 1:
					return $this->CrudData->$method($params[0]);
					break;
				case 2:
					return $this->CrudData->$method($params[0], $params[1]);
					break;
				default:
					return $this->CrudData->$method($params[0], $params[1], $params[2]);
					break;
			}
		}
	}

	/**
	 * Get the tool list for the requested context
	 * 
	 * @param string $grouping 3 groups, 'model', 'associated', 'record'
	 * @param string $alias
	 * @param string $view 
	 * @return ToolPackage
	 */
	public function useActionPattern($grouping, $alias, $view) {
		$alias = ucfirst($alias);
		$target = ucfirst($grouping) . 'Actions';
		$property = "_$target";
		
		$this->$target = $this->$property->load($alias, $view);
		
		// If we found no actions, check for defaults for this view
		// NEW NEW
		// I think ActionPatter::load() now takes care of this
		// NEW NEW
//		if (empty($this->$target->content)) {
//			$tryDefault = $this->$property->load("default.$view");
//			If (!empty($tryDefault->content)) {
//				$this->$target = $tryDefault;
//			}
//		}
		return $this->$target;
	}
	
	/**
	 * Add an action to the current action pattern set
	 * 
	 * @param string $grouping
	 * @param mixed $path (can be a dot notation string or array)
	 * @param mixed $data (can be array or boolean)
	 * @param boolean $replace
	 * @return type
	 */
	public function addActionPattern($grouping, $path, $data = FALSE, $replace = FALSE) {
		
		$target = '_' . ucfirst($grouping) . 'Actions';
		$this->$target->add($path, $data, $replace);
	}
		
	/**
	 * Make the chosen CrudData and its matching Field object the current ones
	 * 
	 * @param string $alias
	 * @return object CrudData object
	 */
	public function useCrudData($alias) {
		if (!isset($this->_CrudData)) {
			$this->_CrudData = new Collection();
		}
		if ($this->_CrudData->has($alias)) {
			$this->CrudData = $this->_CrudData->load($alias);
			$this->currentModel = $alias;
			
			$this->currentStrategy = $this->CrudData->strategy();
			$this->Renderer = $this->DecorationSetups->make($this->currentStrategy);
			
			$this->setActions();
			
			// THIS IS BEING REFACTORED TO HAPPEN SEPARATELY
			// BUT IS USED IN 6 PLACES
//			$this->Field = $this->createFieldHandler($this->CrudData->strategy());

			return $this->CrudData;
		}
		// need an exception here
	}
	
	public function setActions($alias = NULL, $action = NULL) {
		$alias = is_null($alias) ? $this->currentModel : $alias;
		$action = is_null($action) ? $this->currentStrategy : $action;
		$this->ModelActions = $this->_ModelActions->load($alias, $action);
		$this->AssociationActions = $this->_AssociationActions->load($alias, $action);
		$this->RecordActions = $this->_RecordActions->load($alias, $action);
	}

		/**
	 * The call to get product for you page. Will also do default setup if it's not done yet
	 * 
	 * The $column had better be one of the indexes in CrudData->column() or 
	 * your going to burn to the ground.
	 * 
	 * Also needs to have CrudData set to one of the _CrudData objects. But if it's not 
	 * the object matching the current controller name will be used.
	 * 
	 * And needs a Field strategy to be selected. But if it's not, the one associated 
	 * with the current CrudData object will be used. Or a default.
	 * 
	 * @param string $column the field name/column name
	 * @return mixed probably a string
	 */
	public function output($column) {
		list($model, $column) = stristr($column, '.') ? explode('.', $column) : [$this->request->controller, $column];
		if (!$this->CrudData->aliasIs($this->currentModel)) {
			$this->useCrudData($model);
		}
		if (!$this->CrudData->strategyIs($this->currentStrategy)) {
            $this->currentStrategy = $this->CrudData->strategy();
			$this->Renderer = $this->DecorationSetups->make($this->currentStrategy);
		}
		return $this->Renderer->output($column, $this->CrudData->columns()[$column]['attributes']);
	}
	
	
	
	public function currentStrategy($strategy = false) {
		if ($strategy) {
			$this->currentStrategy = $strategy;
		}
		return $this->currentStrategy;
	}

	public function crudState($mode) {
		switch ($mode) {
			case 'save':
				$current_alias = (is_object($this->CrudData)) ? $this->CrudData->alias('string') : FALSE;
				array_push($this->_aliasStack, $current_alias);
				break;
			
			case 'restore':
				$restored_alias = array_pop($this->_aliasStack);
				if($restored_alias){
					$this->useCrudData($restored_alias);
				} else {
					unset($this->CrudData);
				}
				break;

			default:
				break;
		}
	}
	
	/**
	 * var_dump output
	 * 
	 * The list of 'property' names and their values
	 * 
	 * @return array
	 */
	public function __debugInfo() {
		$properties = [
			'_nativeModelActionPatterns', 
			'_associatedModelActionPatterns',
			'_recordActionPatterns',
			'ModelActions',
			'AssociationActions',
			'RecordActions',			
			'_ModelActions',
			'_AssociationActions',
			'_RecordActions',			
//			'_nativeModelActionDisplay',
//			'_associatedModelWhitelist',
//			'_associatedModelBlacklist',
//			'_CrudData',
//			'CrudData'
		];
		foreach ($properties as $name) {
			$properties[$name] = $this->$name;
		}
		return $properties;
	}
	
	public function swapEntity($new_entity) {
		$this->holdEntity = clone $this->entity;
		$this->entity = $new_entity;
	}
	
	public function restoreEntity() {
		$this->entity = clone $this->holdEntity;
	}
}