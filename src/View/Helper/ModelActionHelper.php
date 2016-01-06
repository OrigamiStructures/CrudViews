<?php

namespace CrudViews\View\Helper;

use Cake\View\Helper;

/**
 * RecordActionHelper - The output generators for differnt requested tools
 * 
 * This class may move or be split to defaults vs custom. The biggest 
 * problem with this stub is the many repeated arguments. And even with that,
 * there is no room for custom arguments.
 * 
 * These are the Html generators for the Record goup tool actions. 
 * The default tools are links to controller/action/id in a couple of slight 
 * variations. But you can return any Html as the page module to handle your 
 * tool. The example() method belows shows a form being returned.
 * 
 * @author dondrake
 */
class ModelActionHelper extends Helper {
		
	public $helpers = ['Html', 'Form'];

	/**
	 * stub implementation for the record action tool output
	 * 
	 * defaults to controller/action/id links
	 * if a named action is found, return that instead
	 * 
	 * @param string $tool
	 * @param string $label
	 * @param string|object $name \CrudViews\Lib\NameConvention
	 * @return string Html output
	 */
	public function output($tool, $label, $alias = '', $entity = NULL) {
//		debug(get_class($alias));
		if (get_class($alias) !== 'CrudViews\Lib\NameConventions') {
			$alias = new CrudViews\Lib\NameConventions($alias);
		}
//	debug($tool);
//	debug($label);
//	debug($alias);
//	debug($entity);die;
		// if there's a named action do it
		if (method_exists($this, $tool)) {
			return $this->$tool($label, $entity, $alias);
			
		// otherwise do a link to controller = name, action = tool
		} else {
//			debug($tool);
			$targetName = $alias->pluralHumanName;
			if (in_array($tool, ['new', 'add'])) {
				$targetName = $alias->singularHumanName;
			}
//			debug($tools->parse->action($tool));
			return $this->Html->link(
					__("$label $targetName"), 
					['controller' => $alias->variableName, 'action' => $tool]
			);
		}
	}
	
	/**
	 * Standard CRUD delete link
	 * 
	 * @param object $tools
	 * @param string $tool
	 * @param object $name NameConvention
	 * @return type
	 */
	public function delete($label, $entity){
		return $this->Form->postLink(
				__($label), 
				['action' => 'delete', $entity->id], 
				['confirm' => __('Are you sure you want to delete # {0}?', $entity->id)]);
	}
	
	public function edit($label, $entity, $alias){
		return $this->Html->link(
				__($label . ' ' . $alias->singularHumanName), 
				['action' => 'edit', $entity->id]
			);
	}
	
	/**
	 * An example that returns more than just a link
	 * 
	 * @param type $tools
	 * @param type $tool
	 * @param type $entity
	 * @return type
	 */
	public function example($tools, $tool, $name){
		return '<form>' . $this->Form->input('example', ['label' => $tools->parse->label($tool)]) . '<button>Click</button></form>';
	}
	
	public function search($tools, $tool, $name) {
		return $this->_View->element('CrudViews.simple_search');
	}
}
