<?php

namespace CrudViews\View\Helper\CRUD\Decorator;

use Cake\Utility\Inflector;
use CrudViews\Lib\NameConventions;

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
class BelongsToDecorator extends FieldDecorator {
	
	protected $belongsTo = NULL;

	public function output($field, $options = array()) {
		// if there is a override type on the field, don't make it a belongsTo link
		if (!in_array($this->helper->columnType($field), $this->helper->override())) {
			
			// if this is a belongsTo field, make it a link to the parent record
			if (in_array($field, $this->helper->foreignKeys()) && $this->belongsTo = $this->fieldIsKey($field, 'manyToOne')) {
				
				$nameable = new NameConventions($field);
				$association_name = strtolower($nameable->modelName);
				
				// This is the name of the associated entity. It will also be 
				// the node on the parent entity where the associated entity 
				// can be found (the conventional location)
				$association_entity = Inflector::singularize($association_name);
				// This is going to contain the associated Table object
				$association = $this->helper->CrudData->associationCollection()->get($association_name);

				if (!is_null($association) && is_subclass_of($this->helper->entity->$association_entity, 'Cake\ORM\Entity')) {
					$displayField = $association->displayField();
					$output = $this->helper->entity->$association_entity->$displayField;
					
//					$this->helper->CrudData->_columns[$field] = ['type' => 'string']; ////// THIS CASCADES TO ATTRIBUTES TOO
//					
//					$this->helper->swapEntity($this->helper->entity->$association_entity);
//					$output = $this->base->output($displayField, $options);
//					$this->helper->restoreEntity();
					
				} else {
					$output = $this->base->output($field, $options);
				}

				return ( $this->helper->entity->has($this->belongsTo['property']) ?
								$this->helper->Html->link(
										$output, //This should be a reference to the associate model's display
										[
									'controller' => $this->belongsTo['name'],
									'action' => 'view',
									$this->helper->entity->$field //This should be a reference to the associate model's primary key
										]
								) :
								'' );
			}			
		}
		return $this->base->output($field, $options);
	}

	protected function fieldIsKey($field, $type) {

		$associations = collection($this->helper->associations())->filter(function($association, $key) use ($field) {
			return $association['foreign_key'] === $field;
		});
		foreach ($associations as $association) {
			if (isset($this->helper->foreignKeys()[$field]) &&
				!$association['owner'] &&
				$association['association_type'] === $type) {
				return $association;
			}			
		}
		return false;
	}

}
