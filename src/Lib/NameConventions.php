<?php
namespace CrudViews\Lib;

use Cake\Core\ConventionsTrait;
use Cake\Utility\Inflector;

/**
 * NameConventions
 *
 * @author Don Drake
 */
class NameConventions {
	
	use ConventionsTrait;
	
	protected $_name;

	public function __construct($name) {
		$this->_name = $name;
	}
	
	/**
	 * Magic access to the methods as properties
	 * 
	 * @param string $version The inflection version requested as a property name
	 * @return string|NULL The inflected string or NULL
	 */
	public function __get($version) {
		$method = "_$version";
		if (method_exists($this, $method)) {
			return $this->$method($this->_name);
		} else {
			return NULL;
		}
	}
	
	/**
	 * Magic output if the object is echoed
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->_name;
	}
	
	/**
	 * var_dump output
	 * 
	 * The list of 'property' names and their values
	 * 
	 * @return array
	 */
	public function __debugInfo() {
			$ar = [
				'echo $thisObject' => $this->_name,
				'protected properties' => [
					"_name" => $this->_name 
				],
				'public properties'	=> []
			];
			$methods = get_class_methods($this);
			foreach ($methods as $method) {
				if (stristr($method, '__')) {
					continue;
				}
				$label = str_replace('_', '', $method);
				$ar['public properties'][$label] = $this->$method($this->_name);
			}
			return $ar;
	}
	
	/**
	 * A convenience 'property'
	 * 
	 * If someone has the object on $alias and they ask for $alias->name this will fulfill the request
	 * 
	 * @param string $name
	 * @return string
	 */
	protected function _name($name) {
		return $name;
	}
	
	/**
	 * This one doesn't make sense in this class
	 * 
	 * But I've fixed it so it will return a valid result
	 * 
	 * @param string $key
	 * @return NULL
	 */
	protected function _modelNameFromKey($name) {
        $key = str_replace('_id', '', $this->_modelKey($name));
        return Inflector::camelize(Inflector::pluralize($key));
	}
	
	/**
	 * for a modelName request
	 * 
	 * @param string $key
	 * @return string
	 */
	protected function _modelName($name) {
        return $this->_modelNameFromKey($this->_modelKey($name));
	}
}
