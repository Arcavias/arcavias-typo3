<?php


class tx_scheduler_TestTask extends tx_scheduler_Task
{
	public function execute()
	{
	}
}


class tx_scheduler_Module
{
	public $CMD;

	public function addMessage( $message, $severity )
	{
	}
}


class Tx_Arcavias_Tests_Unit_Scheduler_Provider_Typo4Test
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;


	public function setUp()
	{
		if( interface_exists( 'TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface' ) ) {
			$this->markTestSkipped( 'Test is for TYPO3 4.x only' );
		}

		$this->_object = new Tx_Arcavias_Scheduler_Provider_Typo4();
	}


	public function tearDown()
	{
		unset( $this->_object );
	}


	/**
	 * @test
	 */
	public function getAdditionalFields()
	{
		$taskInfo = array();
		$module = new tx_scheduler_Module();
		$module->CMD = 'edit';

		$result = $this->_object->getAdditionalFields( $taskInfo, $this->_object, $module );

		$this->assertInternalType( 'array', $result );
		$this->assertArrayHasKey( 'arcavias_controller', $result );
		$this->assertArrayHasKey( 'arcavias_sitecode', $result );
		$this->assertArrayHasKey( 'arcavias_config', $result );
	}


	/**
	 * @test
	 */
	public function getAdditionalFieldsException()
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( Tx_Arcavias_Scheduler_Base::getContext() );

		$taskInfo = array();
		$module = new tx_scheduler_Module();
		$module->CMD = 'edit';

		MShop_Locale_Manager_Factory::injectManager( 'MShop_Locale_Manager_Default', $manager );
		$result = $this->_object->getAdditionalFields( $taskInfo, $this->_object, $module );
		MShop_Locale_Manager_Factory::injectManager( 'MShop_Locale_Manager_Default', null );

		$this->assertEquals( array(), $result );
	}


	/**
	 * @test
	 */
	public function saveAdditionalFields()
	{
		$data = array(
			'arcavias_sitecode' => 'testsite',
			'arcavias_controller' => 'testcntl',
			'arcavias_config' => 'testconf',
		);
		$task = new tx_scheduler_TestTask();

		$this->_object->saveAdditionalFields( $data, $task );

		$this->assertEquals( 'testsite', $task->arcavias_sitecode );
		$this->assertEquals( 'testcntl', $task->arcavias_controller );
		$this->assertEquals( 'testconf', $task->arcavias_config );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsNoController()
	{
		$data = array();
		$module = new tx_scheduler_Module();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsNoSite()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
		);
		$module = new tx_scheduler_Module();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsNoSiteFound()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
			'arcavias_sitecode' => 'testsite',
			'arcavias_config' => 'testconf',
		);
		$module = new tx_scheduler_Module();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFields()
	{
		$data = array(
			'arcavias_sitecode' => 'default',
			'arcavias_controller' => 'catalog/index/optimize',
		);
		$module = new tx_scheduler_Module();

		$this->assertTrue( $this->_object->validateAdditionalFields( $data, $module ) );
	}
}
