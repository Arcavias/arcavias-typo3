<?php


class Tx_Arcavias_Tests_Unit_Controller_AccountControllerTest
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;
	private $_flashMessages;


	public function setUp()
	{
		$this->_object = $this->getAccessibleMock( 'Tx_Arcavias_Controller_AccountController', null );

		$objManager = new Tx_Extbase_Object_ObjectManager();

		$this->_flashMessages = new Tx_Extbase_MVC_Controller_FlashMessages();
		$uriBuilder = $objManager->create( 'Tx_Extbase_MVC_Web_Routing_UriBuilder' );
		$response = $objManager->create( 'Tx_Extbase_MVC_Web_Response' );
		$request = $objManager->create( 'Tx_Extbase_MVC_Web_Request' );

		$uriBuilder->setRequest( $request );

		$this->_object->injectFlashMessageContainer( $this->_flashMessages );
		$this->_object->_set( 'uriBuilder', $uriBuilder );
		$this->_object->_set( 'response', $response );
		$this->_object->_set( 'request', $request );

		$this->_object->_call( 'initializeAction' );
	}


	public function tearDown()
	{
		unset( $this->_object, $this->_flashMessages );
	}


	/**
	 * @test
	 */
	public function historyAction()
	{
		$output = $this->_object->historyAction();

		$this->assertStringStartsWith( '<section class="arcavias account-history', $output );
	}


	/**
	 * @test
	 */
	public function historyActionException()
	{
		$name = 'Client_Html_Account_History_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->historyAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}
}