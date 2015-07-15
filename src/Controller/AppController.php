<?php
namespace CrudViews\Controller;

use Cake\Controller\Controller;
use CrudViews\Model\Table\CrudData;
use CrudViews\Lib\CrudConfig;
use CrudViews\View\Helper\CrudHelper;
use Cake\Event\Event;

class AppController extends Controller {
	
	public $helpers = ['CrudViews.Crud'];

	use CrudConfig;
	
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

}
