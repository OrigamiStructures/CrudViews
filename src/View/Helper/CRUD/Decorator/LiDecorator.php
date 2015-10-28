<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\ColumnDecorator;

/**
 * Description of TableCellDecorator
 *
 * @author dondrake
 */
class LiDecorator extends ColumnDecorator {
	
	public function output($field, $options = array()) {
		$tag = false;
		if (isset($options['li'])) {
			$tag = $this->helper->Html->tag('li', NULL, $this->helper->CrudData->attributes("$field.li"));
		}
		return ($tag ? $tag : '<li>') . $this->base->output($field, $options);
	}

}
