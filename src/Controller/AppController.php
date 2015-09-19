<?php
namespace CrudViews\Controller;

use Cake\Controller\Controller;
use CrudViews\Model\Table\CrudData;
use CrudViews\Lib\CrudConfig;
use CrudViews\View\Helper\CrudHelper;
use Cake\Event\Event;

class AppController extends Controller {
	
//	public $helpers = ['CrudViews.Crud'];
	
	protected $_dynamicActions = [];
	
	/**
	 * The actions that will be considered 'standard'
	 *
	 * @var array
	 */
	protected $_standardActions = FALSE;

	/**
	 * The actions that are considered 'standard' by default
	 *
	 * @var array
	 */
	protected $_crudActions = ['view', 'index', 'edit', 'add'];

	use CrudConfig;
	
	public function initialize() {
		parent::initialize();
	}
	
	public function beforeFilter(Event $event) {
		$this->dynamic = FALSE;
//		debug($this->_dynamicActions);
//		debug("{$this->request->controller}.{$this->request->action}");
		
		if ($this->_dynamicActions === TRUE || in_array("{$this->request->controller}.{$this->request->action}", $this->_dynamicActions)) {
//			debug('this one is connected');
//			debug($this->_CrudData->load($this->request->controller)->strategy());
			if (!isset($this->_CrudData) || 
					!$this->_CrudData->has($this->request->controller) && 
					$this->_CrudData->load($this->request->controller)->strategy() !== $this->request->action) 
				{
//				debug('and it needs setup');
				if (in_array($this->request->action, $this->_crudActions)) {
					$method = 'config' . ucfirst($this->request->action);
					$this->$method($this->request->controller);
//					debug('standard');
				} else {
					$method = $this->request->action;
					// need an exception check here
					$this->$method();
//					debug('custom');
				}
//				die;
			} else {
//				debug('but it doesn\'t need setup');
//				debug($this->_CrudData->load($this->request->controller));
			}
			$this->dynamic = TRUE;
		}
		
	}
	
	/**
	 * Return or set the standard actions eligible for CrudView control in any Controller
	 * 
	 * These aren't the views actually controlled. They are the views that will be 
	 * considered 'standard' during the connectCrudViews() process
	 * 
	 * No argument returns the configured action list. 
	 * Defaults to index, view, edit, add. 
	 * Provide an array to replace or extend the list.
	 * 
	 * @param array $actions the actions to use or add to the list
	 * @param boolean $overwrite defaults to TRUE
	 * @return array
	 */
	public function standardActions($actions = [], $overwrite = TRUE) {
		if (empty($actions)) {
			if (!$this->_standardActions) {
				$this->_standardActions = $this->_crudActions;
			}
			return $this->_standardActions;
		}
		$defaults = array_combine($this->_crudActions, $this->_crudActions);
		$actions = array_combine((array) $actions, (array) $actions);
		if ($overwrite) {
			$this->_standardActions = $actions;
		} else {
			$this->_standardActions = array_merge($actions, $defaults);
		}
		return $this->_standardActions;
	}

	/**
	 * Identify which Controller/Actions should use dynamic crud views
	 * 
	 * values for $action and thier results:
	 *	'all' - will make all controllers use dynamic views for all standard views
	 *	'Menus' - (where Menus is a controller) use dynamic views for all standard views in this controller
	 *	'Menus.index' - (where Menus is a controller) use dynamic views for 'index' in this controller
	 *	['Menus.index', 'User.edit', 'Articles'] - Yep. Pass in an array of settings
	 * 
	 * @param string|array $actions 
	 */
	public function connectCrudViews($actions = []) {
		if (is_string($actions)) {
			if (strtolower($actions) === 'all') {
				$this->_dynamicActions = TRUE;
				return $this->_dynamicActions;
			}
			$actions = (array) $actions;
		}
		foreach ($actions as $action) {
			if (!stristr($action, '.')) {
				foreach ($this->standardActions() as $crudAction) {
					$this->_dynamicActions["$action.$crudAction"] = "$action.$crudAction";
				}
			} else {
				$this->_dynamicActions[$action] = $action;
			}
		}
//		debug($this->_dynamicActions);
		return $this->_dynamicActions;
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
	 * @return string
	 */
    public function render($view = null, $layout = null) {
		if ($this->dynamic && in_array($this->request->action, $this->_crudActions)) {
			// This needs a place to put user created dynamic views?
			$view = is_null($view) ? "CrudViews.CRUD/{$this->request->action}" : $view;
			if (is_null($layout)) {
				$layout = (!$this->layout) ? 'default' : $this->layout;
			}
			// This still might need to detect custom action hookups
			// right now it only works for the standard views
			// but I think this is actually all we will want it to do
			// needs review and discussion
			return parent::render($view, $layout);
		} else {
			parent::render($view, $layout);
		}
		return $this->response;
	}

}
