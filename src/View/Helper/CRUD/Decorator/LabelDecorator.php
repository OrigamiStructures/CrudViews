<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\ColumnDecorator;
use CrudViews\Lib\NameConventions;

/**
 * Description of LabelDecorator
 *
 * @author dondrake
 */
class LabelDecorator extends ColumnDecorator{

	public function output($field, $options = array()) {
		
		$name = new NameConventions($field);
//		return '<p><span>' . $name->singularHumanName . ': </span>' . $this->base->output($field, $options) . "</p>\n";
		$attributes = $this->helper->CrudData->attributes($field, 'p');
		$class = isset($attributes['class']) ? $attributes['class'] : '';
		$this->Html->para(
			$class, 
			$this->Html->tag(
				'span', 
				$name->singularHumanName . ': ', 
				$this->helper->CrudData->attributes("$field.span")
			) . $this->base->output($field, $options),
			$attributes);
	}
	
}
