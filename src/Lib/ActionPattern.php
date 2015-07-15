<?php
namespace CrudViews\Lib;

use CrudViews\Lib\Collection;

/**
 * ActionPattern - manage the deep Action Patterns that back up the Crud Veiw model and record tool generation
 * 
 * Model and Record tool sets are stored in multi-layered structures.
 * The finished object (which this class returns and managages) is stored 
 * in one of three properties in CrudHelper. 
 * <pre>
 * property = CollectionObject -> [
 *		'name/alias' => CollectionObject [
 *			'view/name' => Object 
 *				->content = [array of actions and labels]
 *				->parser = tools to return a label or action given a content node
 *			]
 *		]
 * So, on one of the three properties, the use should be able to specify the 
 * model by alias and the view by its action name and get back the object that 
 * has an array of tools for that context.
 * 
 * There are tools here to make new structures, add information to structures, 
 * replace sections with new information, and remove sections completely. 
 * 
 * There are also tools to get all or part of a structure
 * 
 * ----------
 * Currently, the three properties hold Model actions, Associated Model actions, 
 * and Record actions. All three start with a 'default' set of actions if no 
 * model alias is known. The defaults will take care of cake-standard crud patterns.
 * ----------
 *
 * @author dondrake
 */
class ActionPattern {
	
	protected $group;
	
	private $alias_level;
	
	private $view_level;
	
	private $target;
	
	public $ToolParser;
	
	public $action_template;


	public function __construct($data = FALSE) {
		$this->ToolParser = new ToolParser();
		$this->action_template = (object) ['content' => [], 'parse' => $this->ToolParser, 'keys' => []];
		$this->group = new Collection();
		if ($data) {
			$this->addModels($data, TRUE);
		}
	}

	/**
	 * Add or overwrite one or more action sets in the collection
	 * 
	 * This will effect an alias level. All the current settings for any 
	 * referenced alias will be removed and replaced by the new settings.
	 * 
	 * <pre>
	 * [
	 *	'alias' => [
	 *		'view' => ['action', ['label' => 'action'], 'action'],
	 *		'view' => ['action']
	 *	],
	 *	'more-as-desired' => ['view' => ['action']]
	 * ]
	 * </pre>
	 * 
	 * @param array $aliasSettings
	 */
	protected function addModels($aliasSettings, $replace = FALSE) {

		foreach ($aliasSettings as $alias => $viewSettings) {
			$this->target = $this->group;
			if ($replace) {
				$this->newLevel($alias, new Collection());
			} else {
				$this->insureLevel($alias, new Collection());
			}
			$this->alias_level = $this->group->load($alias);
			$this->addViews($viewSettings, $replace);
		}
		$this->clearScratchpad();
	}
	
	private function clearScratchpad() {
		unset($this->target, $this->alias_level, $this->view_level);
	}


	protected function addViews($viewSettings, $replace = FALSE) {
		foreach ($viewSettings as $view => $toolSettings) {
			$this->target = $this->alias_level;
			if ($replace) {
				$this->newLevel($view, clone $this->action_template);
			} else {
				$this->insureLevel($view, clone $this->action_template);
			}
			$this->view_level = $this->alias_level->load($view);
			$this->addTools($toolSettings, $view, $replace);
		}
	}
	
	protected function addTools($toolSettings, $view, $replace = FALSE) {
//		$this->target = $this->view_level;
		$content = [];
		foreach ($toolSettings as $action) {
			$content[$this->ToolParser->action($action)] = $action;
		}
		if ($replace) {
			$this->target->add($view, (object) ['content' => $content, 'parse' => $this->ToolParser, 'keys' => array_keys($content)]);
		} else {
			$content = array_merge($this->target->load($view)->content, $content);
			$this->target->load($view)->content = $content;
			$this->target->load($view)->keys = array_keys($content);
		}
	}
	
	public function load($path) {
		if (!is_string($path)) {
			return $this->action_template;
		}
		$levels = explode('.', $path);
		switch (count($levels)) {
			case 1: // alias level stable ->add('Users', [])
				$this->target = $this->group;
				$this->insureLevel($levels[0], new Collection());
				return $this->group->load($levels[0]);
				break;
			case 2: // view level stable ->add('Users.index', [])
				$this->target = $this->group;
				$this->insureLevel($levels[0], new Collection());
				$this->target = $this->alias_level = $this->group->load($levels[0]);
				$this->insureLevel($levels[1], $this->action_template);
				return $this->alias_level->load($levels[1]);
				break;
		}
	}

	public function add($path, $data = FALSE, $replace = FALSE) {
		if (is_array($path)) {
			$this->addModels($path, $data); // which are actually $data, $replace in this case
			
		} elseif (is_string($path)) {
			$levels = explode('.', $path);
			switch (count($levels)) {
				case 1: // alias level stable ->add('Users', [])
					$this->target = $this->group;
					$this->insureLevel($levels[0], new Collection());
					$this->alias_level = $this->group->load($levels[0]);
					$this->addViews($data, $replace);
					break;
				case 2: // view level stable ->add('Users.index', [])
					$this->target = $this->group;
					$this->insureLevel($levels[0], new Collection());
					$this->target = $this->alias_level = $this->group->load($levels[0]);
					$this->insureLevel($levels[1], $this->action_template);
					$this->view_level = $this->alias_level->load($levels[1]);
					$this->addTools($data, $levels[1], $replace);
					break;
			}
		}
		$this->clearScratchpad();
	}
	
	protected function insureLevel($key, $data = NULL) {
		if (!$this->target->has($key)) {
			$this->newLevel($key, $data);
		}
	}

	protected function newLevel($key, $data = NULL) {
		$this->target->remove($key);
		$this->target->add($key, $data);
	}
		
}
