<?php
namespace CrudViews\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CrudViews\Model\Table\CrudData;
use App\Model\Table\TimesTable;

/**
 * CrudViews\Model\Table\CrudDataTable Test Case
 */
class CrudDataTest extends TestCase
{
    public $CrudData;
    public $Times;
    public $columnKeys;
    
    
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Times') ? [] : ['className' => 'App\Model\Table\TimesTable'];
        $this->Times = TableRegistry::get('Times', $config);
        $this->CrudData = new CrudData($this->Times);
        $this->columnKeys = array_keys($this->CrudData->columns());
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
//        unset($this->CrudData);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->assertObjectHasAttribute("_whitelist", $this->CrudData);
        $this->assertNotEmpty($this->columnKeys);
//        $this->markTestIncomplete('Not implemented yet.');
    }
    
    /**
     * Test the whitelist function with an empty whitelist
     * 
     * @dataProvider whitelistProvider
     */
    public function testWhitelistEmpty($whitelist, $blacklist, $replace, $expected) {
        $this->CrudData->blacklist($blacklist);
        $this->assertEquals($expected, $this->CrudData->whitelist($whitelist, $replace));
    }
    
    /**
     * Build as:
     * [
     * [whitelistArray, blacklistArray, replaceBoolean, resultArray]
     * [whitelistArray-1, blacklistArray-1, replaceBoolean-1, resultArray-1]
     * ]
     */
    public function whitelistProvider() {
        $columnKeys = [
            'id',
            'created',
            'modified',
            'user_id',
            'project_id',
            'time_in',
            'time_out',
            'activity',
            'status',
            'task_id',
            'os_billing_status',
            'customer_billing_statusCopy'
        ];
        $return = [
            //Test-0
            [
                [], [], FALSE, $columnKeys
            ],
            //Test-1
            [
                [], [], TRUE, $columnKeys
            ],
            //Test-2
            [
                ['id', 'created', 'modified'], [], FALSE, ['id', 'created', 'modified']
            ],
            //Test-3
            [
                [], ['id'], FALSE, array_diff($columnKeys, ['id'])
            ],
            //Test-4
            [
                ['id', 'created', 'modified'], ['id'], FALSE, ['id', 'created', 'modified']
            ]
        ];
        return $return;
    }

    /**
     * Test the whitelist function with a pre-populated whitelist
     * 
     * @dataProvider whitelistProviderPopulated
     */
    public function testWhitelistPopulated($whitelist, $blacklist, $replace, $expected, $debug = FALSE) {
        $this->CrudData->blacklist($blacklist);
        $this->CrudData->whitelist(['activity', 'status', 'task_id'], TRUE);
        $this->assertEquals($expected, $this->CrudData->whitelist($whitelist, $replace));
    }
    
    
    /**
     * Build as:
     * [
     * [whitelistArray, blacklistArray, replaceBoolean, resultArray]
     * [whitelistArray-1, blacklistArray-1, replaceBoolean-1, resultArray-1]
     * ]
     */
    public function whitelistProviderPopulated() {
        $populatedWhitelist = [
            'activity',
            'status',
            'task_id'
        ];
        
        $columnKeys = [
            'id',
            'created',
            'modified',
            'user_id',
            'project_id',
            'time_in',
            'time_out',
            'activity',
            'status',
            'task_id',
            'os_billing_status',
            'customer_billing_statusCopy'
        ];
        $return = [
            //Test-0
            [
                [], [], FALSE, $populatedWhitelist
            ],
            //Test-1
            [
                [], [], TRUE, $columnKeys
            ],
//            Test-2
            [
                ['id', 'created', 'modified'], [], FALSE, ['activity', 'status', 'task_id', 'id', 'created', 'modified']
            ],
            //Test-3
            [
                [], ['id'], FALSE, $populatedWhitelist
            ],
            //Test-4
            [
                ['id', 'created', 'modified'], [], TRUE, ['id', 'created', 'modified']
            ],
            //Test-5
            [
                $populatedWhitelist, [], FALSE, $populatedWhitelist
            ],
        ];
        return $return;
    }
    
    /**
     * Test the blacklist function
     * 
     * @dataProvider blacklistProvider
     */
    public function testBlacklist($pre_pop, $blacklist, $replace, $expected) {
        $this->CrudData->blacklist($pre_pop, TRUE);
        $this->assertEquals($expected, $this->CrudData->blacklist($blacklist, $replace));
    }
    
    /**
     * Build as:
     * 
     */
    public function blacklistProvider() {
        return [
            //test0
            [
                [],[],FALSE,[]
            ],
            //test1
            [
                [],[],TRUE,[]
            ],
            //test2
            [
                [],['id'],FALSE,['id']
            ],
            //test3
            [
                [],['id'],TRUE,['id']
            ],
            //test4
            [
                ['creation'],[],FALSE,['creation']
            ],
            //test5
            [
                ['creation'],[],TRUE,[]
            ],
            //test6
            [
                ['creation'],['id'],FALSE,['creation', 'id']
            ],
            //test7
            [
                ['creation'],['id'],TRUE,['id']
            ],
            
        ];
    }
	
	/**
	 * 
	 * @param type $column
	 * @param type $schema
	 * @param type $expected
	 */
	public function testColumns($column, $schema, $expected) {
		$this->assertEquals($expected, $this->CrudData->columns($column, $schema));
	}
}
