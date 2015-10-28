<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\ColumnDecorator;

/**
 * Description of ResponsiveHeadDecorator
 *
 * @author dondrake
 */
class ResponsiveHeadDecorator extends ColumnDecorator {
	
	public function output($field, $options = array()) {
		return $this->helper->Html->tag('div', $this->helper->Paginator->sort($field), $this->helper->CrudData->attributes("$field.div"));
	}

}
