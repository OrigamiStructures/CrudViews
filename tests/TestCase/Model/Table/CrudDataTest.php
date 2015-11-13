<?php
namespace CrudViews\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CrudViews\Model\Table\CrudData;
use App\Model\Table\TimesTable;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

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
            [ [], [], FALSE, $populatedWhitelist ],
            //Test-1
            [ [], [], TRUE, $columnKeys ],
//            Test-2
            [ ['id', 'created', 'modified'], [], FALSE, 
			  ['activity', 'status', 'task_id', 'id', 'created', 'modified'] ],
            //Test-3
            [ [], ['id'], FALSE, $populatedWhitelist ],
            //Test-4
            [ ['id', 'created', 'modified'], [], TRUE, ['id', 'created', 'modified'] ],
            //Test-5
            [ $populatedWhitelist, [], FALSE, $populatedWhitelist ],
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
            [ [],[],FALSE,[] ],
            //test1
            [ [],[],TRUE,[] ],
            //test2
            [ [],['id'],FALSE,['id'] ],
            //test3
            [ [],['id'],TRUE,['id'] ],
            //test4
            [ ['creation'],[],FALSE,['creation'] ],
            //test5
            [ ['creation'],[],TRUE,[] ],
            //test6
            [ ['creation'],['id'],FALSE,['creation', 'id'] ],
            //test7
            [ ['creation'],['id'],TRUE,['id'] ],
        ];
    }
	
	public function columnsProvider () {
		$schema_source = TRUE;
		$property_source = FALSE;
		return [
			// [column_name, source, verification_key, verification_result]
			/*TEST 0*/
			[NULL, $property_source, // args
				'id',				 // verif-key
				['type' => 'integer', 'attributes' => []] // verif-val
			],
			/*TEST 1*/
			[NULL, $schema_source,		// args
				'id',					// verif-key
				['type' => 'integer',	// verif-val
				'length' => 11,
				'unsigned' => false,
				'null' => false,
				'default' => null,
				'comment' => '',
				'autoIncrement' => true,
				'precision' => null				
			]],
			/*TEST 2*/
			['project_id', $property_source,	// args
				'project_id',					// verif-key
				['foreign_key' => true,			// verif-val
					'type' => 'integer', 
					'attributes' => []]
			],
			/*TEST 3*/
			['project_id', $schema_source,	// args
				'project_id',				// verif-key
				['type' => 'integer',		// verif-val
				'length' => (int) 11,
				'unsigned' => false,
				'null' => true,
				'default' => null,
				'comment' => '',
				'precision' => null,
				'autoIncrement' => null]
			],
			/*TEST 4*/
			['bad_column', $property_source, // args
				'bad_column',				 // verif-key
				NULL // verif-val
			],
			/*TEST 5*/
			['bad_column', $schema_source, // args
				'bad_column',				 // verif-key
				NULL // verif-val
			],
		];
	}
	
	/**
	 * @dataProvider columnsProvider
	 */
	public function testColumns($column, $schema, $eval_column, $expected) {
		if (is_string($column)) {
			$this->assertEquals($expected, $this->CrudData->columns($column, $schema));
		} else {
			$this->assertEquals($expected, $this->CrudData->columns($column, $schema)[$eval_column]);
			$this->assertCount(12, $this->CrudData->columns($column, $schema));
		}
	}
	
	public function addColumnProvider() {
		return [
			// TEST 0
			['duration', ['type' => 'decimal', 'precision' => 2, 'null' => TRUE], // args
				['type' => 'decimal', 'attributes' => []], // prop_expected
				['type' => 'decimal',					   // schema expected
				'precision' => (int) 2,
				'length' => null,
				'null' => TRUE,
				'default' => null,
				'comment' => null,
				'unsigned' => null],
				FALSE										// pre_existing
			],
			['project_id', ['type' => 'decimal', 'precision' => 2, 'null' => TRUE], // args
				['type' => 'decimal', 
				'attributes' => ['class' => 'existing'],
				'foreign_key' => true],					   // prop_expected
				['type' => 'decimal',					   // schema expected
				'length' => null,
				'unsigned' => null,
				'null' => true,
				'default' => null,
				'comment' => null,
				'precision' => 2],
				TRUE										// pre_existing
			],
		];
	}
	
	/**
	 * @dataProvider addColumnProvider
	 */
	public function testAddColumn($column, $specs, $prop_expected, $schema_expected, $pre_existing) {
		if ($pre_existing) {
			$this->CrudData->addAttributes($column, ['class' => 'existing']);
		}
		$this->CrudData->addColumn($column, $specs);

		$this->assertEquals($prop_expected, $this->CrudData->columns($column));
		$this->assertEquals($schema_expected, $this->CrudData->columns($column, TRUE));
	}
	
	public function overrideProvider() {
		$c0 = $c1 = $c2 = array (
		  'time_in' => array ('type' => 'timestamp'),
		  'time_out' => array ('type' => 'timestamp')
		);
		
		$c1['time_in']['type'] = 'datetime';
		
		$c2['time_in']['type'] = 'random';
		$c2['time_out']['type'] = 'datetime';
				
		return [
			// test 1, defaults - no overrides
			[$c0, FALSE, NULL],
			// test 2, set override with string-key, string-val
			[$c1, 'time_in', 'datetime'],
			// test 3, set multiple overrides via array
			[$c2, ['time_in' => 'random', 'time_out' => 'datetime'], NULL]
		];
	}

	/**
	 * @dataProvider overrideProvider
	 */
	public function testOverride($expected, $arg1, $arg2) {
		$this->assertEquals(
				Hash::extract($expected, 'time_{n}.type'), 
				Hash::extract($this->CrudData->override($arg1, $arg2), 'time_{n}.type'));
	}
}
