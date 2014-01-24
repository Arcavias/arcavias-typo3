<?php


class Tx_Arcavias_Tests_Unit_Controller_BasketControllerTest
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;
	private $_flashMessages;


	public function setUp()
	{
		$this->_object = $this->getAccessibleMock( 'Tx_Arcavias_Controller_BasketController', null );

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
	public function indexAction()
	{
		$output = $this->_object->indexAction();

		$this->assertStringStartsWith( '<section class="arcavias basket-standard', $output );
	}


	/**
	 * @test
	 */
	public function indexActionException()
	{
		$name = 'Client_Html_Basket_Standard_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Basket_Standard_Factory::injectClient( $name, $client );
		$output = $this->_object->indexAction();
		Client_Html_Basket_Standard_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function smallAction()
	{
		$output = $this->_object->smallAction();

		$this->assertStringStartsWith( '<section class="arcavias basket-mini', $output );
	}


	/**
	 * @test
	 */
	public function smallActionException()
	{
		$name = 'Client_Html_Basket_Mini_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Basket_Standard_Factory::injectClient( $name, $client );
		$output = $this->_object->smallAction();
		Client_Html_Basket_Standard_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}
}