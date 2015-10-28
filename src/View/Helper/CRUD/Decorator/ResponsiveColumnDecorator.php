<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\ColumnDecorator;

/**
 * Description of ResponsiveColumnDecorator
 *
 * @author dondrake
 */
class ResponsiveColumnDecorator extends ColumnDecorator {
	
	public function output($field, $options = array()) {
//		debug($this->helper->CrudData->attributes($field, 'div'));
		return $this->helper->Html->tag('div', $this->base->output($field, $options), $this->helper->CrudData->attributes("$field.div"));
//		return '<td>' . $this->base->output($field, $options) . '</td>';
	}

}
