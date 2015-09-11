<?php
namespace CrudViews\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use CrudViews\View\Helper\CRUD\ToolPackage;
use CrudViews\Lib\Collection;
use Cake\Utility\Inflector;
use CrudViews\Lib\NameConventions;
use CrudViews\Lib\CrudConfig;
use CrudViews\View\Helper\CRUD\Decorator\TableCellDecorator;
use CrudViews\View\Helper\CRUD\Decorator\BelongsToDecorator;
use CrudViews\View\Helper\CRUD\CrudFields;
use CrudViews\View\Helper\CRUD\Decorator\LabelDecorator;
use CrudViews\Template\CRUD\Exception\MissingFieldSetupFileException;
use \CrudViews\Template\CRUD\Exception\MissingFieldSetupException;
use Cake\Core\Exception\Exception;

//Here's the location of your custom FieldSetups
use App\View\Helper\CrudViewResources\FieldSetups;

class CrudHelper extends Helper
{
	
	public $helpers = ['Html', 'Form', 'Text', 'CrudViews.RecordAction', 'CrudViews.ModelAction'];
	
	use CrudConfig;

	public $ModelActions;
	public $AssociationActions;
	public $RecordActions;

	protected $_nativeModelActionDisplay = TRUE; 
	
	protected $_associatedModelWhitelist;
	
	protected $_associatedModelBlacklist;
	
	/**
	 * The default (assumed) model alias (derived from the current controller)
	 *
	 * @var string
	 */
	protected $_defaultAlias;
	
	/**
	 * The current crud data object
	 *
	 * @var CrudData object
	 */
	public $CrudData;
	
	/**
	 * Instance of some CrudField sub-type to do field-vlaue output (possibly wrapped in decorators)
	 * 
	 * CrudField sub-classes will do default 'view' output for all the standard field types 
	 * and EditField sub-classes will do default 'input' generation for all the types
	 * 
	 * Decorators on them must be subclasses of FieldDecorator
	 *
	 * @var CrudField
	 */
	public $Field;
	
	/**
	 * The class that will contain your customer output configurations
	 *
	 * @var object
	 */
	public $FieldSetups;

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
		$this->_Field = new Collection();
				
		foreach ($config['actions'] as $name => $pattern) {
			$this->{$name} = $pattern;
		}   
		$this->useCrudData($this->_defaultAlias->name);
    }
    
	
    protected function loadFieldSetups() {
		debug('fopen');
        $handle = fopen(
				env('DOCUMENT_ROOT') . DS. 'src' . DS . 'View' . DS . 'Helper' . DS . 'CrudViewResources' . DS . 'FieldSetups.php',
				'r');
        if(!$handle){
			debug('throw');
            throw new MissingFieldSetupFileException(['fSetup' => 'FieldSetup File', 'another' => 'thing', 'plugin' => 'CrudViews']);
			debug('done throwing');
        } else {
            fclose($handle);
            return $this->FieldSetups = new FieldSetups($this);
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
		
		switch (strtolower($grouping)) {
			case 'model':
				$target = 'ModelActions';
				$property = "_$target";
				break;
			case 'association':
				$target = 'AssociationActions';
				$property = "_$target";
				break;
			case 'record':
				$target = 'RecordActions';
				$property = "_$target";
				break;
			default:
				return []; // !!!!!**** This should throw some error. Must be one of the three to be valid
				break;
		}
		$this->$target = $this->$property->load("$alias.$view");
		
		// If we found no actions, check for defaults for this view
		if (empty($this->$target->content)) {
			$tryDefault = $this->$property->load("default.$view");
			If (!empty($tryDefault->content)) {
				$this->$target = $tryDefault;
			}
		}
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
		
		switch (strtolower($grouping)) {
			case 'model':
				$target = '_ModelActions';
				break;
			case 'association':
				$target = '_AssociationActions';
				break;
			case 'record':
				$target = '_RecordActions';
				break;
			default:
				return []; // !!!!!**** This should throw some error. Must be one of the three to be valid
				break;
		}
		$this->$target->add($path, $data, $replace);
	}
	
	/**
	 * Get the alias for the current CrudData object
	 * 
	 * @param string $type 'string' = string name, other value for NameConvention object for name
	 * @return string|object
	 */
	public function alias($type = 'object') {
		if ($type === 'string') {
			return $this->CrudData->alias()->name;
		} else {
			return $this->CrudData->alias();
		}
	}
	
	public function columns() {
			return $this->CrudData->columns();
	}

	public function column($name) {
		return $this->CrudData->column($name);
	}
	
	public function override($types = FALSE) {
		return $this->CrudData->override($types);
	}
	
	public function columnType($name) {
		return $this->CrudData->columnType($name);
	}
	
	/**
	 * Get the primary key(s) for the current CrudData
	 * 
	 * @return array
	 */
	public function primaryKey($as_array = FALSE) {
		return $this->CrudData->primaryKey($as_array);
	}
	
	public function foreignKeys() {
		return $this->CrudData->foreignKeys();
	}
	
	public function associations() {
		return $this->CrudData->associations();
	}
	
	/**
	 * Get the dispayField for the current CrudData
	 * 
	 * @return string
	 */
	public function displayField() {
		return $this->CrudData->displayField();
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
			$this->Field = $this->createFieldHandler($this->CrudData->strategy());
//			$this->useField($alias);
			return $this->CrudData;
		}
	}
	
	/**
	 * Establish the Field to use for output
	 * 
	 * Make the chosen Field object the current one or 
	 * if the requested one doesn't exist, let the current 
	 * one stand. There will always be one, so no worries.
	 * 
	 * @param string $alias
	 */
//	public function useField($alias) {
//		if ($this->_Field->has($alias)) {
//			$this->Field = $this->_Field->load($alias);
//		}
//	}
	
	/**
	 * The call to get product for you page. Will also do default setup if it's not done yet
	 * 
	 * The $field had better be one of the indexes in CrudData->column() or 
	 * your going to burn to the ground.
	 * 
	 * Also needs to have CrudData set to one of the _CrudData objects. But if it's not 
	 * the object matching the current controller name will be used.
	 * 
	 * And needs a Field strategy to be selected. But if it's not, the one associated 
	 * with the current CrudData object will be used. Or a default.
	 * 
	 * @param string $field the field name/column name
	 * @return mixed probably a string
	 */
	public function output($field) {
		$dot = stristr($field, '.') ? explode('.', $field) : FALSE;
		
		// we can at least have a fallback output strategy
		if (!$this->Field) {
			$this->_Field->add($this->alias('string'), $this->createFieldHandler($this->request->action));
			$this->Field = $this->_Field->load($this->alias('string'));
		}
		if (!$dot && !isset($this->CrudData)) {
			$this->useCrudData($this->alias('string'));
		} elseif ($dot) {
			$field = $dot[1];
			$this->useCrudData($dot[0]);
			// shouldn't this also check to see if there is a field output strategy for this $dot[0]?
		}
		return $this->Field->output($field, $this->columns()[$field]['attributes']);
	}
	
	/**
	 * Put a field output strategy in place
	 * 
	 * There are two base flavors of output strategy for fields, 
	 * 'view' and 'edit'. Each establishes one default output product for 
	 * every field type. Once a strategy is in place (and CrudData is in place 
	 * to provide the columns data) we can send a field name to the output() 
	 * method and the product for that field type will be returned.
	 * 
	 * Custom setups can be created in two ways. 
	 * 
	 * First the output strategies can be decorated. The decorator may add to the 
	 * output by adding DOM tags to every field (see TableCellDecorator), it may 
	 * perform logic and modify some fields, leaving other untouched (see BelongsToManyDecorator), 
	 * or it may perform other logic and interventions like substituting new content 
	 * in place of, or near some fields.
	 * Decorators should all extend FieldDecorator class.
	 * 
	 * Secondly, non-standard field types can be defined and set on the columns property 
	 * of CrudData through the override() method and property. New sub classes of CrudField 
	 * or EditField can be made that add processing for the new type. The type 'image' is a  
	 * possible example. Normally a field with an image name in it would output as a string. 
	 * An 'image' extension for CrudField would render an image tag. The EditField extension 
	 * would render a file type input. 
	 * 
	 * The cake-standard crud patterns are pre-defined. Methods can be added to the FieldSetup 
	 * class for custom set-ups and the name of the method can be passed in as $action. If the 
	 * requested method isn't found, LabelDecorator/CrudFields will be returned. It will 
	 * output <p><span>Field Name: </span>field-value</p>
	 * 
	 * @param string $action name of the output construction process to use
	 */
	public function createFieldHandler($action) {
		
		// Is actually override-strategy-for-fields-in-this-action
		if ($this->CrudData->overrideAction($action)) {
			$action = $this->CrudData->overrideAction($action);
		}
				
		switch ($action) {
			// the four cake-standard crud setups
			case 'index':
//				debug('setup index decoration');
				return new TableCellDecorator(
					new BelongsToDecorator(
						new CrudFields($this)
					));
				break;
			case 'view':
//				debug('setup view decoration');
				return new BelongsToDecorator(
						new CrudFields($this)
					);
				break;
			case 'edit':
			case 'add':
				return new CrudFields($this);
				break;

			// your custom setups or the default result if your's isn't found
			default:
				if (!isset($this->FieldSetups)) {
					$this->loadFieldSetups();
				}
				if (method_exists($this->FieldSetups, $action)) {
					debug('method');
					return $this->FieldSetups->$action($this);
				} else {
					debug('exception');
					throw new MissingFieldSetupException(['action' => $action, 'more' => 'more']);
				}
//				if (method_exists($this->FieldSetups, $action)) {
//					return $this->FieldSetups->$action($this);
//				} else {
//					return new LabelDecorator(new CrudFields($this));
//				}
		}
	}
	
	public function addAttributes($field, $attributes) {
		$this->CrudData->addAttributes($field, $attributes);
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
			'_nativeModelActionDisplay',
			'_associatedModelWhitelist',
			'_associatedModelBlacklist',
			'_CrudData',
			'CrudData'
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