<?php
namespace CrudViews\View\Helper\CRUD\Decorator;

use CrudViews\View\Helper\CRUD\Decorator;
use Cake\Utility\Text;
use CrudViews\View\Helper\CRUD\CrudFields;
use CrudViews\View\Helper\CRUD\Decorator\TableCellDecorator;
use CrudViews\View\Helper\CRUD\Decorator\BelongsToDecorator;
use CrudViews\View\Helper\CRUD\Decorator\BelongsToSelectDecorator;
use CrudViews\View\Helper\CRUD\Decorator\LiDecorator;
use CrudViews\View\Helper\CRUD\Decorator\LinkDecorator;

/**
 * BasicDecorationSetups are the decoration patterns for basic crud views
 * 
 * These are the decorator layers that will be applied to each column as it is 
 * output. The decorators may contain filter logic so they only operate on 
 * some columns (like the BelongsToDecorator) or they may apply universally.
 * 
 * This class should also be inherited by the user's DecorationStrategy which 
 * is where custom strategies can be written.
 *
 * @author dondrake
 */
class BasicDecorationSetups {

	protected $helper;

	public function __construct($helper) {
		$this->helper = $helper;
		$this->product = $this->{$helper->currentStrategy}($helper);
	}
	
	public function index($helper) {
		return new TableCellDecorator(
			new BelongsToDecorator(
				new CrudFields($helper)
		));
	}
	
	public function view($helper) {
		return new BelongsToDecorator(
			new CrudFields($helper)
		);
	}
	
	public function edit($helper) {
		return new CrudFields($this);
	}
	
	public function add($helper) {
		return new CrudFields($this);
	}

}
