<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

/**
 * BelongsToDecorator will make a standard link to owning records
 * 
 * This will watch every field and if it finds that the field is the 
 * foreign key in a belongsTo, it will turn the key into a link to the 
 * owning record. The link will be to the view action and will follow 
 * cake conventions.
 * 
 * eg:
 * $field = time_bomb_id
 * and it is found to be used in a manyToOne (belongsTo) relationship,
 * the link will go to /time_bombs/view/{time_bomb_id}
 *
 * @author dondrake
 */
class BelongsToSelectDecorator extends BelongsToDecorator {
	
	public function output($field, $options = array()) {
		$attributes = [
				'empty' => 'choose one',
				'label' => FALSE,
				'value' => $this->helper->entity->$field
			];
		if($this->hasUuid()) {
//			debug($this->helper->entity);
			$attributes['form'] = $this->helper->entity->_uuid->uuid('form');
		}
		if ($this->fieldIsKey($field, 'manyToOne')) {
			return $this->helper->Form->input($field,$attributes);
		}
		return $this->base->output($field, $options);
	}

}
