<?php


class tx_scheduler_Module
{
	public $CMD;

	public function addMessage( $message, $severity )
	{
	}
}


class Tx_Arcavias_Tests_Unit_Scheduler_DefaultTest
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;


	public function setUp()
	{
		$this->_object = new tx_arcavias_scheduler_default();
	}


	public function tearDown()
	{
		unset( $this->_object );
	}


	/**
	 * @test
	 */
	public function execute()
	{
		$result = $this->_object->execute();

		$this->assertTrue( $result );
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
	public function saveAdditionalFields()
	{
		$data = array(
			'arcavias_sitecode' => 'testsite',
			'arcavias_controller' => 'testcntl',
			'arcavias_config' => 'testconf',
		);

		$this->_object->saveAdditionalFields( $data, $this->_object );

		$this->assertEquals( 'testsite', $this->_object->arcavias_sitecode );
		$this->assertEquals( 'testcntl', $this->_object->arcavias_controller );
		$this->assertEquals( 'testconf', $this->_object->arcavias_config );
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
