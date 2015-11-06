<?php
namespace CrudViews\Lib;

/**
 * ToolParser gives simplified access to memebers of tool arrays
 * 
 * The arrays follow the pattern
 * <pre>
 *	[
 *		'label' => 'target-action',
 *		'label-action',
 *	],
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
class ToolParser {
	
//	public function __construct() {
//		return $this;
//	}
	
	/**
	 * Get the proper label for this tool entry
	 * 
	 * @param string $tool The value of the array node (not the key)
	 * @param string $suffix Additional words for the label (typically the model alias)
	 * @return string First word capitalized
	 */
	public function label($tool, $suffix = '') {
		if (is_array($tool)) {
			$label = array_keys($tool)[0];
		} else {
			$label = $tool;
		}
		return ucwords($label . (empty($suffix) ? '' : " $suffix"));
	}
	
	/**
	 * Get the action that will be this tools action
	 * 
	 * [ tool-name => tool-name ]
	 * 
	 * @param string $tool The value of the array node (not the key)
	 * @return string The action that will be the tool action
	 */
	public function action($tool) {
		if (is_array($tool)) {
			$tool = $tool[array_keys($tool)[0]];
		}
		return $tool;
	}
}
