<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\FieldDecorator;
use CrudViews\Lib\NameConventions;

/**
 * Description of LabelDecorator
 *
 * @author dondrake
 */
class LabelDecorator extends FieldDecorator{

	public function output($field, $options = array()) {
		
		$name = new NameConventions($field);
		return '<p><span>' . $name->singularHumanName . ': </span>' . $this->base->output($field, $options) . "</p>\n";
		
	}
	
}
