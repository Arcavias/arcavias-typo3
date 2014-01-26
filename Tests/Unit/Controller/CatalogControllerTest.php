<?php


class Tx_Arcavias_Tests_Unit_Controller_CatalogControllerTest
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;


	public function setUp()
	{
		$this->_object = $this->getAccessibleMock( 'Tx_Arcavias_Controller_CatalogController', array( 'dummy' ) );

		$objManager = new Tx_Extbase_Object_ObjectManager();

		$uriBuilder = $objManager->get( 'Tx_Extbase_MVC_Web_Routing_UriBuilder' );
		$response = $objManager->get( 'Tx_Extbase_MVC_Web_Response' );
		$request = $objManager->get( 'Tx_Extbase_MVC_Web_Request' );

		$uriBuilder->setRequest( $request );

		if( method_exists( $response, 'setRequest' ) ) {
			$response->setRequest( $request );
		}

		$this->_object->_set( 'uriBuilder', $uriBuilder );
		$this->_object->_set( 'response', $response );
		$this->_object->_set( 'request', $request );

		$this->_object->_call( 'initializeAction' );
	}


	public function tearDown()
	{
		unset( $this->_object );
	}


	/**
	 * @test
	 */
	public function detailAction()
	{
		$name = 'Client_Html_Catalog_Detail_Default';
		$client = $this->getMock( $name, array( 'getBody', 'getHeader', 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->detailAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 'body', $output );
	}


	/**
	 * @test
	 */
	public function detailActionException()
	{
		$name = 'Client_Html_Catalog_Detail_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Catalog_Detail_Factory::injectClient( $name, $client );
		$output = $this->_object->detailAction();
		Client_Html_Catalog_Detail_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( t3lib_FlashMessageQueue::getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function filterAction()
	{
		$name = 'Client_Html_Catalog_Filter_Default';
		$client = $this->getMock( $name, array( 'getBody', 'getHeader', 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->filterAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 'body', $output );
	}


	/**
	 * @test
	 */
	public function filterActionException()
	{
		$name = 'Client_Html_Catalog_Filter_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Catalog_Filter_Factory::injectClient( $name, $client );
		$output = $this->_object->filterAction();
		Client_Html_Catalog_Filter_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( t3lib_FlashMessageQueue::getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function filtersearchAction()
	{
		$name = 'Client_Html_Catalog_Filter_Default';
		$subname = 'Client_Html_Catalog_Filter_Search_Default';

		$client = $this->getMock( $name, array( 'getSubClient', 'process' ), array(), '', false );
		$subclient = $this->getMock( $subname, array( 'getBody', 'getHeader', 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getSubClient' )->will( $this->returnValue( $subclient ) );
		$subclient->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$subclient->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->filtersearchAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 'body', $output );
	}


	/**
	 * @test
	 */
	public function filtersearchActionException()
	{
		$name = 'Client_Html_Catalog_Filter_Default';
		$client = $this->getMock( $name, array( 'getSubClient' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getSubClient' )->will( $this->throwException( new Exception() ) );

		Client_Html_Catalog_Filter_Factory::injectClient( $name, $client );
		$output = $this->_object->filtersearchAction();
		Client_Html_Catalog_Filter_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( t3lib_FlashMessageQueue::getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function listAction()
	{
		$name = 'Client_Html_Catalog_List_Default';
		$client = $this->getMock( $name, array( 'getBody', 'getHeader', 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->listAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 'body', $output );
	}


	/**
	 * @test
	 */
	public function listActionException()
	{
		$name = 'Client_Html_Catalog_List_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Catalog_List_Factory::injectClient( $name, $client );
		$output = $this->_object->listAction();
		Client_Html_Catalog_List_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( t3lib_FlashMessageQueue::getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function listsimpleAction()
	{
		$name = 'Client_Html_Catalog_List_Simple';
		$client = $this->getMock( $name, array( 'getBody', 'getHeader', 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->listsimpleAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 'body', $output );
	}


	/**
	 * @test
	 */
	public function listsimpleActionException()
	{
		$name = 'Client_Html_Catalog_List_Simple';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Catalog_List_Factory::injectClient( $name, $client );
		$output = $this->_object->listsimpleAction();
		Client_Html_Catalog_List_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( t3lib_FlashMessageQueue::getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function stageAction()
	{
		$name = 'Client_Html_Catalog_Stage_Default';
		$client = $this->getMock( $name, array( 'getBody', 'getHeader', 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->stageAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 'body', $output );
	}


	/**
	 * @test
	 */
	public function stageActionException()
	{
		$name = 'Client_Html_Catalog_Stage_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Catalog_Stage_Factory::injectClient( $name, $client );
		$output = $this->_object->stageAction();
		Client_Html_Catalog_Stage_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( t3lib_FlashMessageQueue::getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function stockAction()
	{
		$name = 'Client_Html_Catalog_Stock_Default';
		$client = $this->getMock( $name, array( 'getBody', 'getHeader', 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );

		Client_Html_Account_History_Factory::injectClient( $name, $client );
		$output = $this->_object->stockAction();
		Client_Html_Account_History_Factory::injectClient( $name, null );

		$this->assertEquals( 'body', $output );
	}


	/**
	 * @test
	 */
	public function stockActionException()
	{
		$name = 'Client_Html_Catalog_Stock_Default';
		$client = $this->getMock( $name, array( 'process' ), array(), '', false );

		$client->expects( $this->once() )->method( 'process' )->will( $this->throwException( new Exception() ) );

		Client_Html_Catalog_Stock_Factory::injectClient( $name, $client );
		$output = $this->_object->stockAction();
		Client_Html_Catalog_Stock_Factory::injectClient( $name, null );

		$this->assertEquals( 1, count( t3lib_FlashMessageQueue::getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}
}