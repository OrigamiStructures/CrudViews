<?php

/*
 * CrudData provides data to support tailoring CRUD elements for thier Models
 * 
 * This trait provides simple arrays that aid with automating the use of Models.
 * It can also provide an object that provides basic information and logic about Model 
 * fields and relationships.
 * 
 * The foreignKeys data always contains information on all the associations for the Model. 
 * The columns data can contain information about all the columns in the Model, or the data  
 * can filtered by a whitelist or blacklist. If a whitelist is present, that will be used, 
 * if not, and a blacklist is present the column list will be filtered. If neither is 
 * present, all columns will be returned.
 * 
 */

namespace CrudViews\Model\Table;

use Cake\Core\ConventionsTrait;
use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Table;
use CrudViews\Lib\NameConventions;
use Bake\Utility\Model\AssociationFilter;
use \Cake\Utility\Hash;

define('COLUMNS', FALSE);
define('SCHEMA', FALSE);

/**
 * CakePHP CrudData
 * @author dondrake
 */
class CrudData {

	use ConventionsTrait;
	use InstanceConfigTrait;

	/**
	 * This is just for reference. 
	 * 
	 * These are the standard cake types. all datasource specific types
	 * get translated into one of these types
	 *
	 * @var type 
	 */
	private $column_types = ['date', 'time', 'datetime', 'timestamp',
		'boolean',
		'biginteger',
		'integer',
		'uuid',
		'string',
		'binary',
		'float',
		'decimal'
	];

	/**
	 * The AssociationCollection for this model
	 *
	 * @var object AssociationCollection
	 */
	protected $AssociationCollection;

	/**
	 * The foreign keys in this table and its associations
	 * 
	 * @var array
	 */
	protected $_foreign_keys;

	/**
	 * The associations in this table and info about them
	 * 
	 * <pre>[
	 *  fk (string, the name) => [
	 *    'owner' => boolean, is this table the owner of the association,
	 *    'association_type' => string, the type of association,
	 *    'name' => string, the alias for the association,
	 *    'property' => string, name of the entity property that will contain the associated data
	 *   ],
	 *  fk => [
	 *    ...
	 *   ]
	 *  ]</pre>
	 *
	 * @var array
	 */
	protected $_associations;

	/**
	 * An array of all the BelongsTo objects for this model 
	 *
	 * @var array
	 */
//	protected $_belongs_to;

	/**
	 * fields to return in the columns list
	 * 
	 * If set, this will be the list of columns returned 
	 * regardless of any blacklist setting
	 *
	 * @var array
	 */
	protected $_whitelist = [];

	/**
	 * fields to exclude from the columns list
	 * 
	 * If there is a whitelist, these exclusions will be ignored 
	 *
	 * @var array
	 */
	protected $_blacklist = [];
	protected $_override;
	protected $_attributes;
	protected $_defaultConfig;

	/**
	 * An alternate output setup name for a standard crud view
	 * 
	 * index, view, add, edit are hard-mapped to output strategies. If you want 
	 * to use one of these views with a different output strategy, you'll need 
	 * to set this override. ['index' => 'tree_index'] would get the 'tree_index' 
	 * output strategy when 'index' action was in use.
	 *
	 * @var array
	 */
	protected $_overrideAction;

	/**
	 * 
	 *
	 * @var array
	 */
	public $_columns;

	/**
	 * AssociationFilter utility
	 *
	 * @var AssociationFilter
	 */
//    protected $_associationFilter;

	protected $_table;

	/**
	 * The output strategy to use if this is a secondary module
	 * 
	 * If this is output as the primary model, it will use the strategy that 
	 * matches the current action. If This is a CrudData object added to include 
	 * another page module, this is the strategy that will be used.
	 *
	 * @var string
	 */
	protected $_strategy;

	/**
	 * Create a fully populated information object for guiding abstracted output of table data
	 * 
	 * allowed options keys
	 * 'whitelist' -- array of desired fields
	 * 'blacklist' -- array of fields to exclude
	 * 'override' -- hash of fieldnames and types. To force columns to a special type.
	 * whitelist will win if both are present
	 * 
	 * @param \Cake\ORM\Table $table
	 * @param array $options
	 */
	public function __construct(Table $table, $options = []) {

		$this->_blacklist = (isset($options['blacklist'])) ? $options['blacklist'] : [];
		$this->_whitelist = (isset($options['whitelist'])) ? $options['whitelist'] : [];
		$this->_override = (isset($options['override'])) ? $options['override'] : [];
		$this->_overrideAction = (isset($options['overrideAction'])) ? $options['overrideAction'] : [];
		$this->_attributes = (isset($options['attributes'])) ? $options['attributes'] : [];
		$this->_strategy = (isset($options['strategy'])) ? $options['strategy'] : 'index';
		$this->_table = $table;
		
		$this->update();
		$this->_columns = $this->_columns(TRUE);
//		debug($this->_associationFilter);
//		debug($this->AssociationCollection);
//		debug($this->_foreignKeys());die;
	}
	
	/**
	 * Return the table object
	 * 
	 * @return Table
	 */
	public function table() {
		return $this->_table;
	}

	/**
	 * Find the primary key(s) set in the data table
	 * 
	 * SQL tables can have multiple keys. We have always had one. 
	 * $as_array = False returns a single key as a string. 
	 * = True will always return an array of keys. 
	 * 
	 * @param boolean $as_array
	 * @return array
	 */
	public function primaryKey($as_array = FALSE) {
		if ($as_array) {
			return (array) $this->_table->primaryKey();
		} else {
			return $this->_table->primaryKey();
		}
	}

	/**
	 * Find the displayField name
	 * 
	 * @return string
	 */
	public function displayField() {
		return $this->_table->displayField();
	}

	/**
	 * Get the alias of the Table this object describes
	 * 
	 * You can get a string or an inflection object with 
	 * that can return many inflected version of the alias
	 * 
	 * @param string $type
	 * @return NameConventions|string
	 */
	public function alias($type = 'object') {
		if ($type === 'string') {
			return $this->_table->alias();
		} else {
			return new NameConventions($this->_table->alias());
		}
	}

	/**
	 * Set or discover the current strategy
	 * 
	 * @param string $name
	 * @return string
	 */
	public function strategy($name = NULL) {
		if (!is_null($name)) {
			$this->_strategy = $name;
		}
		return $this->_strategy;
	}

	/**
	 * Reestablish core properties for this object
	 */
	public function update() {
		$this->AssociationCollection = $this->_associationCollection($this->_table);
		$this->_foreignKeys = $this->_foreignKeys(TRUE);
//		$this->_columns = $this->_columns(TRUE);
//		$this->_associationFilter = $this->_filteredAssociations();
	}

	/**
	 * Set values in the whitelist
	 * 
	 *	$this->whitelist(['title, article'], TRUE) will overwrite old values
	 *	$this->whitelist(['title, article']) will merge the values
	 * 
	 * @param array $allow
	 * @param boolean $replace
	 * @return array
	 */
	public function whitelist($allow = FALSE, $replace = FALSE) {
		if ($allow !== FALSE) {
			// array provided, so set whitelist
			if ($replace) {
				$this->_whitelist = $allow;
			} else {
                $this->_whitelist = array_keys(array_merge(array_flip($this->_whitelist), array_flip($allow)));
			}
			$this->update();
		}
		// in every case, return the allowed columns
		if (!empty($this->_whitelist)) {
			$allowed = $this->_whitelist;
		} elseif (!empty($this->_blacklist)) {
			$allowed = array_diff(array_keys($this->columns()), $this->_blacklist);
		} else {
			$allowed = array_keys($this->columns());
		}
		return $allowed;
	}

	/**
	 * Set values in the blacklist
	 * 
	 *	$this->blacklist(['id, password'], TRUE) will overwrite old values
	 *	$this->blacklist(['id, password']) will merge the values
	 * 
	 * @param array $deny
	 * @param boolean $replace
	 * @return array
	 */
	public function blacklist($deny = [], $replace = FALSE) {
		if ($deny !== FALSE) {
			// array provided, so set blacklist
			if ($replace) {
				$this->_blacklist = $deny;
			} else {
                $this->_blacklist = array_keys(array_merge(array_flip($this->_blacklist), array_flip($deny)));
			}
		}
		return $this->_blacklist;
	}

	/**
	 * Set an override for a column
	 * 
	 * @param array $types
	 * @param boolean $replace
	 * @return array
	 */
	public function override($types = [], $replace = FALSE) {
		if ($replace) {
			$this->_override = $types;
		}
		if (!empty($types) || $replace) {
			$this->_override += $types;
			$this->update();
		}
		return $this->_override;
	}

	/**
	 * Set and/or return override action
	 * 
	 * The action name will select a named Decoration strategy in CrudHelper. And the 
	 * four standard crud actions are hard-wired. This allows you to substitute 
	 * a different strategy for one of the standards. Or, indeed, to substitute 
	 * a strategy for one of your own views (though I'm not sure why you would).
	 * 
	 * @param string $action
	 * @param string $alternate
	 * @return string or BOOLEAN FALSE
	 */
	public function overrideAction($actionAlternates = [], $replace = FALSE) {
		//pass only a string action to get the alternate back, if already set
		if (is_string($actionAlternates) && isset($this->_overrideAction[$actionAlternates])) {
			return $this->_overrideAction[$actionAlternates];
		} elseif (is_string($actionAlternates) && !isset($this->_overrideAction[$actionAlternates])) {
			return FALSE;
		}
		
		if ($replace) {
			$this->_overrideAction = [];
		}
		if (!empty($actionAlternates) || $replace) {
			while (list($key, $val) = each($actionAlternates)) {
				$this->_overrideAction[$key] = $val;
			}
		}
	}

	/**
	 * Get current foreignKeys array
	 * 
	 * @return array
	 */
	public function foreignKeys() {
		return $this->_foreignKeys;
	}

	/**
	 * Get current associations array
	 * 
	 * @return array
	 */
	public function associations() {
		return $this->_associations;
	}

	
	/**
	 * Get current filtered associations array
	 * 
	 * Not sure what's going on here
	 * 
	 * @return array
	 */
	public function filteredAssociations() {
		return $this->_associationFilter;
	}
	/**
	 * Get all or a single column entry
	 * 
	 * @param void|string $column
	 * @param boolean $schema COLUMNS=internal property SCHEMA=table schema
	 * @return array
	 */
	public function columns($column = NULL, $schema = FALSE) {
		$columns = $schema ?
			$this->_table->schema() :
			$this->_columns;
		$result = NULL;
		if (is_null($column)) {
			$result = $columns;
		} elseif (is_string($column)) {
			$result = isset($columns[$column]) ? $columns[$column] : NULL;
		}
		return $result;		
	}

	/**
	 * Retrieve some attribute entries
	 * 
	 * Attributes are stored as additional arry nodes on the columns array. 
	 * Each column in the entity is accessed by its name as the key and has 
	 * a 'type' node as returned by the schema(). 
	 * Attributes are also stored under thier column and are keyed by the name 
	 * of the DOM element they will apply to as the various decorated levels 
	 * of output are rendered.
	 * 
	 * This method can return any level of the array data.
	 * 
	 * @param array $attributes
	 * @param boolean $replace
	 * @return array
	 */
	public function attributes($attributes = NULL) {
		if (is_null($attributes)) {
			return $this->columns();
		}
		$steps = explode('.', $attributes);
		$steps[0] .= '.attributes';
		$result = Hash::extract($this->columns(), implode('.', $steps));
		return $result;
		return (count($result) === 1) ? $result[0] : $result;
		
	}

	/**
	 * Find the type for a column
	 * 
	 * @param string $field
	 * @return string
	 */
	public function columnType($field) {
		if (isset($this->_columns[$field])) {
			if (isset($this->_override[$field])) {
				return $this->_override[$field];
			}
			return $this->_columns[$field]['type'];
		} else {
			return NULL;
		}
	}

//	public function entityName($name = NULL) {
//		if 
//		return $this->_entityName($this->alias());
//	}
	
	/**
	 * Get the collection of association objects or a particular association object
	 * 
	 * @param name $association The association object to get
	 * @return object AssociationCollection, BelongsTo, BelongsToMany, HasMany or HasOne
	 */
	public function associationCollection($association = null) {
		if (is_null($association)) {
			return $this->_associationCollection();
		} else {
			return $this->_associationCollection()->get($name);
		}
		
	}

	/**
	 * Get the AssociationCollection for this Model
	 * 
	 * @return object
	 */
	protected function _associationCollection() {
		if (!$this->AssociationCollection) {
			$this->AssociationCollection = $this->_table->associations();
		}
		return $this->AssociationCollection;
	}

	/**
	 * Get an array of the foreign keys in this table and information about the associations
	 * 
	 * @return array
	 */
	protected function _foreignKeys($refresh = FALSE) {
		if (!$this->_foreign_keys || $refresh) {
			$this->_foreign_keys = [];
			$this->_associations = [];
			$keys = $this->AssociationCollection->keys();
			foreach ($keys as $assoc_name) {
				$association = $this->AssociationCollection->get($assoc_name);
				$this->_associations[$association->name()] = [
					'foreign_key' => $association->foreignKey(),
					'owner' => $association->isOwningSide($this->_table),
					'class' => get_class($association),
					'association_type' => $association->type(), // oneToOne, oneToMany, manyToMany, manyToOne
					'name' => new NameConventions($association->name()),
					'property' => $association->property()
				];
				$this->_foreign_keys[$association->foreignKey()] = $association->foreignKey();
			}
		}
		return $this->_foreign_keys;
	}
	
	public function addColumn($column, $specs) {
		$this->_table->schema()->addColumn($column, $specs);
		$this->registerColumn($column);
	}
	
	protected function registerColumn($column) {
		$foreign_keys = array_keys($this->foreignKeys());
		$schema = $this->_table->schema();
		if (in_array($column, $foreign_keys)) {
			$this->_columns[$column] = ['foreign_key' => TRUE];
		}
		$this->_columns[$column]['type'] = isset($this->type_override[$column]) ? $this->type_override[$column] : $schema->columnType($column);
		$this->_columns[$column]['attributes'] = isset($this->_columns[$column]['attributes']) ? $this->_columns[$column]['attributes'] : [];
	}

	/**
	 * Get an array of the columns and information about them for this Models table
	 * 
	 * If there is a whitelist, include only these fields. 
	 * If there is no whitelist, but there is a blacklist, exclude these fields
	 * type_override allows forcing a column to a specific type. This will something 
	 * like having an image_name field (normally a text field) return as a `file` field type 
	 * so the proper inputs/outputs can be generated.
	 * 
	 * @return array 
	 */
	protected function _columns($refresh = FALSE) {
		if (!$this->_columns || $refresh) {
			$this->_columns = [];
			$foreign_keys = array_keys($this->foreignKeys());
			$schema = $this->_table->schema();
			$columns = $schema->columns();
			foreach ($columns as $name) {
				if ($this->filterColumn($name)) {
//					debug('removing '.$name);
					continue;
				}
				$this->registerColumn($name);
			}
		}
//		debug($this->_columns);
		return $this->_columns;
	}
	
	/**
	 * Does the whitelist or blacklist filter this column out
	 * 
	 * @param string $name column name
	 * @return boolean 
	 */
	public function filterColumn($name) {
		if (!empty($this->_whitelist)) {
			if (!in_array($name, $this->_whitelist)) {
				return TRUE;
			}
		} elseif (!empty($this->_blacklist)) {
			if (in_array($name, $this->_blacklist)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Get filtered associations
	 * To be mocked...
	 *
	 * @param \Cake\ORM\Table $table Table
	 * @return array associations
	 */
	protected function _filteredAssociations() {
		if (is_null($this->_associationFilter)) {
			$this->_associationFilter = new AssociationFilter();
		}
		return $this->_associationFilter->filterAssociations($this->_table);
	}

	/**
	 * Add an attribute the the array
	 * 
	 * Attributes are stored as additional arry nodes on the columns array. 
	 * Each column in the entity is accessed by its name as the key and has 
	 * a 'type' node as returned by the schema(). 
	 * Attributes are keyed by the name of the DOM element they will apply 
	 * to as the various decorated levels of output are rendered.
	 * 
	 * args:
	 * ( column_name, [ tag_name => [ attr_name => attr_val ]], merge_boolean )
	 * or
	 * (
	 *	 ]
	 *		[ col_name => [ tag_name => [ attr_name => attr_val ]]],
	 *		[ col_name => [ tag_name => [ attr_name => attr_val ]]]
	 *	 ], merge_boolean
	 * )
	 * 
	 * @param string $key
	 * @param string $value
	 * @param boolean $merge
	 */
	public function addAttributes($key = null, $value = null, $merge = true) {
		if (is_array($key)) {
			$merge = is_null($value) ? TRUE : $value;
			foreach ($key as $settings) {
				$key = array_keys($settings)[0];
				$this->_addAttribute($key, $settings[$key], $merge);
			}
		} else {
			$this->_addAttribute($key, $value, $merge);
		}
	}
	
	protected function _addAttribute($column, $attributes, $merge) {
		$this->_defaultConfig = $this->_columns[$column]['attributes'];
		$this->_columns[$column]['attributes'] = $this->config($column, $attributes, $merge)->config()[$column];
	}
	
	public function strategyIs($strategy) {
		return $this->strategy() == $strategy;
	}
	
	public function aliasIs($alias) {
		return $this->_table->alias() == $alias;
	}

}
