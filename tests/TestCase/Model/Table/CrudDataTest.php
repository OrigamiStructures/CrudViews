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
     * @dataProvider whitelistProvider
     */
    public function testWhitelistEmpty($whitelist, $blacklist, $replace, $expected) {
        $this->CrudData->blacklist($blacklist);
        $this->assertEquals($this->CrudData->whitelist($whitelist, $replace), $expected);
    }
    
    /**
     * @dataProvider whitelistProvider
     */
//    public function testWhitelistPopulated($whitelist, $blacklist, $replace, $expected) {
////        debug(array_keys($this->CrudData->columns()));
////        debug($this->CrudData->blacklist);
//        $this->CrudData->blacklist($blacklist);
//        $this->CrudData->whitelist('activity', 'status', 'task_id', TRUE);
////        debug($this->CrudData->whitelist($whitelist, $replace));
//        $this->assertEquals($this->CrudData->whitelist($whitelist, $replace), $expected);
//    }
    
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
            ]
        ];
        return $return;
    }
}
