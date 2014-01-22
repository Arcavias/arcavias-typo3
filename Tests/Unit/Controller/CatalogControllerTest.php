<?php


class Tx_Arcavias_Tests_Unit_Controller_CatalogControllerTest
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;
	private $_flashMessages;


	public function setUp()
	{
		$this->_object = $this->getAccessibleMock( 'Tx_Arcavias_Controller_CatalogController', null );

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
	public function detailAction()
	{
		$output = $this->_object->detailAction();

		$this->assertStringStartsWith( '<section class="arcavias catalog-detail', $output );
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

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function filterAction()
	{
		$output = $this->_object->filterAction();

		$this->assertStringStartsWith( '<section class="arcavias catalog-filter', $output );
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

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function filtersearchAction()
	{
		$output = $this->_object->filtersearchAction();

		$this->assertStringStartsWith( '<section class="catalog-filter-search', $output );
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

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function listAction()
	{
		$output = $this->_object->listAction();

		$this->assertStringStartsWith( '<section class="arcavias catalog-list', $output );
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

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function listsimpleAction()
	{
		$output = $this->_object->listsimpleAction();

		$this->assertStringStartsWith( '[]', $output );
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

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function stageAction()
	{
		$output = $this->_object->stageAction();

		$this->assertStringStartsWith( '<section class="arcavias catalog-stage', $output );
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

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}


	/**
	 * @test
	 */
	public function stockAction()
	{
		$output = $this->_object->stockAction();

		$this->assertContains( 'var stock', $output );
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

		$this->assertEquals( 1, count( $this->_flashMessages->getAllMessagesAndFlush() ) );
		$this->assertNull( $output );
	}
}