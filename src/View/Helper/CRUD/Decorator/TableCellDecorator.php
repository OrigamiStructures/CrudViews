<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\FieldDecorator;

/**
 * Description of TableCellDecorator
 *
 * @author dondrake
 */
class TableCellDecorator extends FieldDecorator {
	
	public function output($field, $options = array()) {
		return $this->helper->Html->tag('td', $this->base->output($field, $options), $this->helper->CrudData->attributes($field, 'td'));
//		return '<td>' . $this->base->output($field, $options) . '</td>';
	}

}
