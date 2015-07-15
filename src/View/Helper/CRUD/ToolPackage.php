<?php
namespace CrudViews\View\Helper\CRUD;

/**
 * ToolPackage gives simplified access to memebers of tool arrays
 * 
 * The arrays follow the pattern
 * <pre>
 *	[
 *		current-action => [
 *			'label' => 'target-action',
 *			'label-action',
 *		],
 *		next-action []
 *	]
 * </pre>
 * This object will contain the label/action list for one 
 * kind of toolset and for a single action. There are 3 groups 
 * of toolsets, 1) for the main model for the controller, 2) for 
 * the associated models off the main model, 3) for individual records. 
 * 
 * The class can tell you what grouping and action the tools 
 * belong to. It can also give you a prepared label and the action 
 * for any tool regardless of its presentation in the array (as array or string)
 *
 * @author dondrake
 */
class ToolPackage {
	
	protected $_gouping;
	
	protected $_action;
	
	protected $_tools;
	
	public function __construct($grouping, $action, $tools) {
		$this->_gouping = $grouping;
		$this->_action = $action;
		$this->_tools = $tools;
	}
	
	/**
	 * Get the value of a property
	 * 
	 * @param string $name
	 * @return string|array
	 */
	public function __get($name) {
		if (property_exists($this, "_$name")) {
			return $this->{"_$name"};
		} else {
			return NULL;
		}
	}
	
	/**
	 * Get the proper label for this tool entry
	 * 
	 * @param string $tool The value of the array node (not the key)
	 * @return string First word capitalized
	 */
	public function label($tool) {
		if (is_array($tool)) {
			return ucfirst(array_keys($tool)[0]);
		} else {
			return ucfirst($tool);
		}
	}
	
	/**
	 * Get the action that will be this tools action
	 * 
	 * @param string $tool The value of the array node (not the key)
	 * @return string The action that will be the tool action
	 */
	public function action($tool) {
		if (is_array($tool)) {
			return $tool[array_keys($tool)[0]];
		} else {
			return ucfirst($tool);
		}
	}
}
