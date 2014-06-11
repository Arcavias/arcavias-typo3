<?php


class tx_scheduler_EmailTask extends tx_scheduler_Task
{
	public function execute()
	{
	}
}


if( !class_exists( 'tx_scheduler_Module' ) )
{
	class tx_scheduler_Module
	{
		public $CMD;

		public function addMessage( $message, $severity )
		{
		}
	}
}


class Tx_Arcavias_Tests_Unit_Scheduler_Provider_Email4Test
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;


	public function setUp()
	{
		if( interface_exists( 'TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface' ) ) {
			$this->markTestSkipped( 'Test is for TYPO3 4.x only' );
		}

		$this->_object = new Tx_Arcavias_Scheduler_Provider_Email4();
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
		$this->assertArrayHasKey( 'arcavias_sender_from', $result );
		$this->assertArrayHasKey( 'arcavias_sender_email', $result );
		$this->assertArrayHasKey( 'arcavias_pageid_detail', $result );
		$this->assertArrayHasKey( 'arcavias_content_baseurl', $result );
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
			'arcavias_pageid_detail' => '123',
			'arcavias_content_baseurl' => 'https://localhost/',
		);
		$task = new tx_scheduler_EmailTask();

		$this->_object->saveAdditionalFields( $data, $task );

		$this->assertEquals( 'testsite', $task->arcavias_sitecode );
		$this->assertEquals( 'testcntl', $task->arcavias_controller );
		$this->assertEquals( 'testconf', $task->arcavias_config );
		$this->assertEquals( 'test name', $task->arcavias_sender_from );
		$this->assertEquals( 'sender@test', $task->arcavias_sender_email );
		$this->assertEquals( 'reply@test', $task->arcavias_reply_email );
		$this->assertEquals( '123', $task->arcavias_pageid_detail );
		$this->assertEquals( 'https://localhost/', $task->arcavias_content_baseurl );
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
			'arcavias_sender_email' => 'sender@test',
		);
		$module = new tx_scheduler_Module();

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
		$module = new tx_scheduler_Module();

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
		$module = new tx_scheduler_Module();

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
		$module = new tx_scheduler_Module();

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
			'arcavias_controller' => 'order/email/delivery',
			'arcavias_sender_email' => 'sender@test',
			'arcavias_pageid_detail' => '123',
			'arcavias_content_baseurl' => 'https://www.arcavias.org:80/up/tx_/',
		);
		$module = new tx_scheduler_Module();

		$this->assertTrue( $this->_object->validateAdditionalFields( $data, $module ) );
	}
}
