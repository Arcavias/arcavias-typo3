<?php


class Tx_Arcavias_Tests_Unit_Scheduler_Provider_Email6Test
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;


	public function setUp()
	{
		if( !interface_exists( 'TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface' ) ) {
			$this->markTestSkipped( 'Test is for TYPO3 6.x only' );
		}

		$this->_object = new Arcavias\Arcavias\Scheduler\Provider\Email6();
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
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();
		$module->CMD = 'edit';

		$result = $this->_object->getAdditionalFields( $taskInfo, $this->_object, $module );

		$this->assertInternalType( 'array', $result );
		$this->assertArrayHasKey( 'arcavias_controller', $result );
		$this->assertArrayHasKey( 'arcavias_sitecode', $result );
		$this->assertArrayHasKey( 'arcavias_config', $result );
		$this->assertArrayHasKey( 'arcavias_sender_from', $result );
		$this->assertArrayHasKey( 'arcavias_sender_email', $result );
		$this->assertArrayHasKey( 'arcavias_reply_email', $result );
	}


	/**
	 * @test
	 */
	public function getAdditionalFieldsException()
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( Tx_Arcavias_Scheduler_Base::getContext() );

		$taskInfo = array();
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();
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
			'arcavias_sender_from' => 'test name',
			'arcavias_sender_email' => 'sender@test',
			'arcavias_reply_email' => 'reply@test',
		);
		$task = new Arcavias\Arcavias\Scheduler\Task\Typo6();

		$this->_object->saveAdditionalFields( $data, $task );

		$this->assertEquals( 'testsite', $task->arcavias_sitecode );
		$this->assertEquals( 'testcntl', $task->arcavias_controller );
		$this->assertEquals( 'testconf', $task->arcavias_config );
		$this->assertEquals( 'test name', $task->arcavias_sender_from );
		$this->assertEquals( 'sender@test', $task->arcavias_sender_email );
		$this->assertEquals( 'reply@test', $task->arcavias_reply_email );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsNoController()
	{
		$data = array();
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();

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
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();

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
			'arcavias_sender_email' => 'sender@test',
		);
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsNoSenderEmail()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
			'arcavias_sitecode' => 'testsite',
			'arcavias_config' => 'testconf',
		);
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsInvalidSenderEmail()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
			'arcavias_sitecode' => 'testsite',
			'arcavias_config' => 'testconf',
			'arcavias_sender_email' => 'sender-test',
		);
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsInvalidReplyEmail()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
			'arcavias_sitecode' => 'testsite',
			'arcavias_config' => 'testconf',
			'arcavias_sender_email' => 'sender@test',
			'arcavias_reply_email' => 'reply-test',
		);
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsInvalidPageID()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
			'arcavias_sitecode' => 'testsite',
			'arcavias_sender_email' => 'sender@test',
			'arcavias_pageid_detail' => 'a',
		);
		$module = new tx_scheduler_Module();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsInvalidBaseurlNoProtocol()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
			'arcavias_sitecode' => 'testsite',
			'arcavias_sender_email' => 'sender@test',
			'arcavias_content_baseurl' => 'localhost',
		);
		$module = new tx_scheduler_Module();

		$this->assertFalse( $this->_object->validateAdditionalFields( $data, $module ) );
	}


	/**
	 * @test
	 */
	public function validateAdditionalFieldsInvalidBaseurlNoDomain()
	{
		$data = array(
			'arcavias_controller' => 'testcntl',
			'arcavias_sitecode' => 'testsite',
			'arcavias_sender_email' => 'sender@test',
			'arcavias_content_baseurl' => 'https:///',
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
			'arcavias_sender_email' => 'sender@test',
			'arcavias_pageid_detail' => '123',
			'arcavias_content_baseurl' => 'https://www.arcavias.org:80/up/tx_/',
		);
		$module = new \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController();

		$this->assertTrue( $this->_object->validateAdditionalFields( $data, $module ) );
	}
}
