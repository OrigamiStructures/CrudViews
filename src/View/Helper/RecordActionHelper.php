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
class RecordActionHelper extends Helper {
	
	public $helpers = ['Html', 'Form'];

	/**
	 * stub implementation for the record action tool output
	 * 
	 * defaults to controller/action/id links
	 * if a named action is found, return that instead
	 * 
	 * @param type $tools
	 * @param type $tool
	 * @param type $entity
	 * @return type
	 */
	public function output($tools, $tool, $entity) {
		if (method_exists($this, $tools->parse->action($tool))) {
			return $this->{$tools->parse->action($tool)}($tools, $tool, $entity);
		} else {
			return $this->Html->link(__($tools->parse->label($tool)), ['action' => $tools->parse->action($tool), $entity->id]);
		}
	}
	
	/**
	 * Standard CRUD delete link
	 * 
	 * @param type $tools
	 * @param type $tool
	 * @param type $entity
	 * @return type
	 */
	public function delete($tools, $tool, $entity){
		return $this->Form->postLink(
				__($tools->parse->label($tool)), 
				['action' => $tools->parse->action($tool), $entity->id], 
				['confirm' => __('Are you sure you want to delete # {0}?', $entity->id)]);
	}
	
	/**
	 * An example that returns more than just a link
	 * 
	 * @param type $tools
	 * @param type $tool
	 * @param type $entity
	 * @return type
	 */
	public function example($tools, $tool, $entity){
		return '<form>' . $this->Form->input('example', ['label' => $tools->parse->label($tool)]) . '<button>Click</button></form>';
	}
	
	public function submit($tools, $tool, $entity) {
		if (isset($entity->_uuid)) {
			$attributes = ['form' => $entity->_uuid->uuid('form')];
		} else {
			$attributes = [];
		}
		return $this->Form->submit($tools->parse->label($tool), $attributes);

	}
	
//	public function move_up($tools, $tool, $entity) {
//		return $this->Html->link($tools->parse->label($tool), ['action' => $tools->parse->action($tool), $entity->id]);
//	}
}
