<?php
namespace CrudViews\Test\TestCase\Lib;

use Cake\TestSuite\TestCase;
use CrudViews\Lib\ActionPattern;
use Cake\Network\Request;

/**
 * Description of ActionPatternTest
 *
 * @author dondrake
 */
class ActionPatternTest extends TestCase{
	
	public $ActionPattern;
	
	public $sample_config = ['default' => [
		'index' => ['new' => 'add'],
		'add' => ['list' => 'index'],
		'view' => ['List' => 'index', 'edit', 'new' => 'add', 'delete'],
		'edit' => ['List' => 'index', 'new' => 'add', 'delete']
	]];

	public function setUp() {
		parent::setUp();
		$this->Request = new Request(['params' => ['controller' => 'activities', 'action' => 'index']]);
//		var_dump($this->Request->action);die;
		$this->ActionPattern = new ActionPattern($this->Request, ['tools' => $this->sample_config]);
		var_dump($this->ActionPattern);die;
//		$this->Request = $this->getMockBuilder('Request')->getMock();
//		var_dump($this->Request);
	}
	
//	public function testSetUp() {
//		$this->assertObjectHasAttribute('group', $this->ActionPattern);
//		$this->assertObjectHasAttribute('action_template', $this->ActionPattern);
//		$this->assertObjectHasAttribute('ToolParser', $this->ActionPattern);
//	}
	
//	public function addProvider () {
//		return [
//			
//		];
//	}
	public function testAdd() {
		$this->ActionPattern->add($this->sample_config);
//		debug($this->ActionPattern->load('Times'));
//		$this->assertEquals(['index', 'add', 'view', 'edit'], $this->ActionPattern->load('Times')->keys());
//		$this->assertEquals(['index' => ['list' => 'index']], $this->ActionPattern->load('Times.add')->content);
//		
//		$this->ActionPattern->add('Times.add', ['search' => 'search']);
//		$this->assertEquals(['index' => ['list' => 'index'], 'search' => 'search'], $this->ActionPattern->load('Times.add')->content);
//		
//		$this->ActionPattern->add('Times.add', ['find' => 'search']);
//		$this->assertEquals(['index' => ['list' => 'index'], 'search' => ['find' => 'search']], $this->ActionPattern->load('Times.add')->content);
//		
//		$this->ActionPattern->add('Times.view', ['action1', 'action2'], TRUE);
//		$this->assertEquals(['action1' => 'action1', 'action2' => 'action2'], $this->ActionPattern->load('Times.view')->content);
	}
}
