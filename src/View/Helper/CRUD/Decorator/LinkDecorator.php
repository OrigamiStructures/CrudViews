<?php

namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator\FieldDecorator;

/**
 * Description of LinkDecorator
 *
 * @author dondrake
 */
class LinkDecorator extends FieldDecorator {

	/**
	 * Wrap some output in an <A> tag
	 * 
	 * Expects the current entity to have four properties:
	 *	controller, action, query, hash
	 * NULL will be used for any that don't exist
	 * 
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function output($field, $options = array()) {

		// we're counting on having the link array overwrite every time so no old values accidentally persist
		$this->helper->addAttributes($field, ['link' =>
			['controller' => $this->helper->entity->controller,
			'action' => $this->helper->entity->action,
			'?' => $this->helper->entity->query,
			'#' => $this->helper->entity->hash]]);

		return $this->helper->Html->link($this->base->output($field, $options), $this->helper->columns()[$field]['attributes']['link']);
	}

}
