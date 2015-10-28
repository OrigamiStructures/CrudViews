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
		return $this->helper->Html->tag('div', $this->base->output($field, $options), $this->helper->CrudData->attributes($field, 'div'));
//		return '<td>' . $this->base->output($field, $options) . '</td>';
	}

}
