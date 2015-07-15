<?php
namespace CrudViews\Lib;

use ArrayIterator;
use FilterIterator;

/**
 * Description of ChildFilter
 *
 * @author dondrake
 */
class ChildFilter extends FilterIterator {
	
	public $property_name;
	public $match_value;
	
	public function __construct(ArrayIterator $iterator, $match_value, $property_name){
        parent::__construct($iterator);
		$this->property_name = $property_name;
		$this->match_value = $match_value;
    }

	public function accept() {
        $entry = $this->getInnerIterator()->current();
		return $entry->{$this->property_name} == $this->match_value;
    }

}
