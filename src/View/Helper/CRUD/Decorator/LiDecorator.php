<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\FieldDecorator;

/**
 * Description of TableCellDecorator
 *
 * @author dondrake
 */
class LiDecorator extends FieldDecorator {
	
	public function output($field, $options = array()) {
		$tag = false;
		if (isset($options['li'])) {
			$tag = $this->helper->Html->tag('li', NULL, $options['li']);
		}
		return ($tag ? $tag : '<li>') . $this->base->output($field, $options);
	}

}
