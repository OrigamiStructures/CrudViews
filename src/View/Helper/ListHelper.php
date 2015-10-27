<?php
namespace CrudViews\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Collection\Collection;
use FilterIterator;
use ArrayIterator;
use ArrayObject;
use CrudViews\Lib\ChildFilter;

/**
 * ListHelper is a recursive list outputer that works inside the decoration system
 * 
 * This helper will send the field to CrudHelper's output method. It should be called through 
 * /Elements/Navigators/li_link.ctp or an equivalent.
 * 
 * This assumes only one field is in the columns list. Since we loop on columns, if there 
 * are more fields, you'll end up with multiple LIs per record.
 * 
 * The calling code will make an instance of this class, sending values to configure the 
 * filter iterator. Then it will make the parent level filter iterator and call the newly 
 * made instance's outputRecursiveLi() method with that and the result object.
 * 
 * The biggest constraints to flexibility of this class are:
 *		- Locked to UL lists. This could be abstracted in the configuration
 *		- The LI attributes are limited to a hard-wired class setting. 
 *			A handler class could be sent in to do the job
 *		- The filter iterator is limited to doing == comparison
 *			The kind of comparison could be abstracted or possibly a closure could be
 *			 sent to do the comparison? This one probably isn't a real limitation though
 * 
 * @author jasont
 */
class ListHelper extends Helper {
	
	public $Crud;
	public $tabs = 0;
	public $filter_property;
	public $filter_match;
	protected $depth;
	protected $classes = [2 => 'second-level', 'third-level', 'fourth-level', 'fifth-level'];


	/**
	 * Set the operating conditions for reursive operation
	 * 
	 * $config keys:
	 *		0 - the CrudHelper object
	 *		filter_property - the property name in child entities that will be tested
	 *		filter_match - the property name in the parent entity that will used in testing
	 *		list_type - UL or OL. Defaults to UL if not spec'd
	 * 
	 * The filter iterator gets configured to match some property in the iterator data set 
	 * to some property value in the current <LI>s entity
	 * 
	 * @param View $View
	 * @param array $config
	 */
	public function __construct(View $View, array $config = array()) {
		$config += ['list_type' => 'ul'];
		parent::__construct($View, $config);

		$this->Crud = $config[0];
		$this->filter_property = $config['filter_property'];
		$this->filter_match = $config['filter_match'];
		$this->list_wrapper = (object) [
			'open' => "<{$config['list_type']}>",
			'close' => "</{$config['list_type']}>"
		];
		$this->depth = 0;
	}

	/**
	 * Recursively process a data set to create a nested <UL>
	 * 
	 * @param FilterIterator $level The flat set of data and the iterator to cherry-pick items for this level
	 * @param Cake\ORM\ResultSet $data Standard results with the array of entities on $data->item
	 * @return string The fully constructed <UL>
	 */
	public function outputRecursiveLi($level, $data) {
		
		// start a new <UL>
		echo str_repeat("\t", $this->tabs) . "{$this->list_wrapper->open}\n";
		
		// loop on the filter iterator
		foreach ($level as $index => $value) {
			$this->Crud->entity = $value;
			// There is always only one entry in columns, the 'label' field for this list
			foreach ($this->Crud->columns() as $column => $details) {
				
				// The class will identify the level of the <LI> in the list heirarchy
				$this->depth += 1; // open an li, consider it a deeper level
				
				$liAttributes = ($this->depth > 1 && $this->depth <= (count($this->classes) + 1)) ? 
					['li' => ['class' => $this->classes[$this->depth]]] :
					['li' => ['class' => '']];
				$this->Crud->addAttributes($column, $liAttributes);

				//generate this <LI> and leave it open for possible nested <UL>
				echo str_repeat("\t", $this->tabs+1) . $this->Crud->output($column, $details) . "\n";
				
				// make the iterator to see children of this open <LI> and call again to render it
				$collection = new ArrayObject($data->toArray());
				$children = new ChildFilter($collection->getIterator(), $value->{$this->filter_match}, $this->filter_property);
				$this->tabs++;
				$this->outputRecursiveLi($children, $data);
				
				// close the <LI>
				$this->depth -= 1; // closing an ul consider it done with the level
				echo str_repeat("\t", $this->tabs--) . "</li>\n";
			}
		}
		// close the <UL> and return
		echo str_repeat("\t", $this->tabs) . "{$this->list_wrapper->close}\n";
		return;
	}
}