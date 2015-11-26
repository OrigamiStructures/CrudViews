<?php
namespace CrudViews\View\Helper\CRUD;

/**
 * FieldOutputInterface sets the required methods for objects that can be Decorated
 *
 * @author dondrake
 */
interface ColumnOutputInterface {
		
	public function output($field);
	
	public function hasUuid();

}
