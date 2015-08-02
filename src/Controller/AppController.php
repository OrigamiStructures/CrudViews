<?php
namespace CrudViews\Controller;

use Cake\Controller\Controller;
use CrudViews\Model\Table\CrudData;
use CrudViews\Lib\CrudConfig;
use CrudViews\View\Helper\CrudHelper;
use Cake\Event\Event;

class AppController extends Controller {
	
//	public $helpers = ['CrudViews.Crud'];
	
	public $dynamicActions = [];
	
	protected $crudActions = ['view', 'index', 'edit', 'add'];

	use CrudConfig;
	
	public function initialize() {
		parent::initialize();
	}
	
	public function beforeFilter(Event $event) {
		$this->dynamic = FALSE;
		
		if ($this->dynamicActions === TRUE || in_array("{$this->request->controller}.{$this->request->action}", $this->dynamicActions)) {
			debug('this one is connected');
//			debug($this->_CrudData->load($this->request->controller)->strategy());
			if (!isset($this->_CrudData) || 
					!$this->_CrudData->has($this->request->controller) && 
					$this->_CrudData->load($this->request->controller)->strategy() !== $this->request->action) 
				{
				debug('and it needs setup');
				if (in_array($this->request->action, $this->crudActions)) {
					$method = 'config' . ucfirst($this->request->action);
					$this->$method($this->request->controller);
					debug('standard');
				} else {
					$method = strtolower($this->request->controller) . ucfirst($this->request->action);
					// need an exception check here
					$this->$method();
					debug('custom');
				}
//				die;
			} else {
				debug('but it doesn\'t need setup');
//				debug($this->_CrudData->load($this->request->controller));
			}
			$this->dynamic = TRUE;
		}
		
	}

	/**
	 * Identify which Controller Actions should use dynamic crud views
	 * 
	 * values for $action and thier results:
	 *	'all' - will make all controllers use dynamic views for all 4 crud views
	 *	'Menus' - (where Menus is a controller) use dynamic views for all 4 crud views in this controller
	 *	'Menus.index' - (where Menus is a controller) use dynamic views for 'index' in this controller
	 *	['Menus.index', 'User.edit', 'Articles'] - Yep. Pass in an array of settings
	 * 
	 * @param string|array $actions 
	 */
	public function connectCrudViews($actions = []) {
		if (is_string($actions)) {
			if (strtolower($actions) === 'all') {
				$this->dynamicActions = TRUE;
				return;
			}
			$actions = array($actions);
		}
		foreach ($actions as $action) {
			if (!stristr($action, '.')) {
				foreach ($this->crudActions as $crudAction) {
					$this->dynamicActions["$action.$crudAction"] = "$action.$crudAction";
				}
			} else {
				$this->dynamicActions[$action] = $action;
			}
		}
		debug($this->dynamicActions);
	}

	/**
	 * Configure the Helper and do automatic render setup if needed
	 * 
	 * @param Event $event
	 */
	public function beforeRender(Event $event) {
		parent::beforeRender($event);

		$this->helpers['CrudViews.Crud'] = [
			'_CrudData' => $this->_CrudData,
			'actions' => [
				'_ModelActions' => $this->_ModelActions,
				'_AssociationActions' => $this->_AssociationActions,
				'_RecordActions' => $this->_RecordActions
			]];

	}
	
	/**
	 * Route to a dynamic view if necessary
	 * 
	 * Override Controller::render()
	 * 
	 * If the developer requested this be a dynamic controller/action and this 
	 * is one of the dynamic actions, render the proper view. Otherwise 
	 * render according to normal rules.
	 * 
	 * @param type $view
	 * @param type $layout
	 * @return type
	 */
    public function render($view = null, $layout = null) {
		if ($this->dynamic && in_array($this->request->action, $this->crudActions)) {
			// This still might need to detect custom action hookups
			// right now it only works for the standard 4
			// but I think this is actually all we will want it to do
			// needs review and discussion
			return parent::render("CrudViews.CRUD/{$this->request->action}");
		} else {
			parent::render($view, $layout);
		}
	}

}
