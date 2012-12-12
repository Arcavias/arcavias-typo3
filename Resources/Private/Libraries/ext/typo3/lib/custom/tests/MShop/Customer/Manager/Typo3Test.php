<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: Typo3Test.php 15573 2012-04-17 15:22:17Z doleiynyk $
 */

/**
 * Test class for MShop_Customer_Manager_Typo3
 * @subpackage Customer
 */
class MShop_Customer_Manager_Typo3Test extends MW_Unittest_Testcase
{
	protected $_context;
	protected $_item = null;
	protected $_object = null;


	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Customer_Manager_Typo3Test');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_object = MShop_Customer_Manager_Factory::createManager( $this->_context, 'Typo3' );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->_object, $this->_item);
	}

	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}

	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Customer_Item_Interface', $item );
	}

	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '~=', 'customer.code', 'unitCustomer' ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No customer item with code "unitCustomer" found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}

	public function testDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'unitCustomer1@metaways.de' ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No customer found.' );
		}

		$this->setExpectedException('MShop_Customer_Exception');
		$this->_object->deleteItem( $item->getId() );
	}

	public function testSaveItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'unitCustomer1@metaways.de' ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No customer found.' );
		}

		$this->setExpectedException('MShop_Customer_Exception');
		$this->_object->saveItem( $item );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
	}


	public function testSearchItems()
	{
		$siteid = $this->_context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'customer.id', null );
		$expr[] = $search->compare( '==', 'customer.label', '' );
		$expr[] = $search->compare( '==', 'customer.code', 'unitCustomer2@metaways.de' );
		$expr[] = $search->compare( '==', 'customer.status', 1 );

		$expr[] = $search->compare( '!=', 'customer.address.id', null );
		$expr[] = $search->compare( '==', 'customer.address.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'customer.address.refid', null );
		$expr[] = $search->compare( '==', 'customer.address.company', 'Metaways GmbH' );
		$expr[] = $search->compare( '==', 'customer.address.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'customer.address.title', '' );
		$expr[] = $search->compare( '==', 'customer.address.firstname', 'Franz-Xaver' );
		$expr[] = $search->compare( '==', 'customer.address.lastname', 'Gabler' );
		$expr[] = $search->compare( '==', 'customer.address.address1', 'Phantasiestraße' );
		$expr[] = $search->compare( '==', 'customer.address.address2', '2' );
		$expr[] = $search->compare( '==', 'customer.address.address3', null );
		$expr[] = $search->compare( '==', 'customer.address.postal', '23643' );
		$expr[] = $search->compare( '==', 'customer.address.city', 'Berlin' );
		$expr[] = $search->compare( '==', 'customer.address.state', 'Berlin' );
		$expr[] = $search->compare( '==', 'customer.address.countryid', 'de' );
		$expr[] = $search->compare( '==', 'customer.address.telephone', '01234509876' );
		$expr[] = $search->compare( '==', 'customer.address.email', 'arcavias@metaways.de' );
		$expr[] = $search->compare( '==', 'customer.address.telefax', '055544333212' );
		$expr[] = $search->compare( '==', 'customer.address.website', 'www.metaways.de' );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		//search without base criteria
		$search = $this->_object->createSearch();
		$this->assertGreaterThanOrEqual( 3, count( $this->_object->searchItems( $search ) ) );

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertGreaterThanOrEqual( 2, count( $results ) );
		$this->assertEquals( $total, count( $results ) );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchConfig()
	{
		$search = $this->_object->createSearch();

		$conditions = array();
		$conditions[] = $search->compare( '>', 'customer.id', 0 );
		$conditions[] = $search->compare( '==', 'customer.label', '' );
		$conditions[] = $search->compare( '==', 'customer.code', 'unitCustomer1@metaways.de');
		$conditions[] = $search->compare( '==', 'customer.status', 1 );

		$search->setConditions( $search->combine( '&&', $conditions ) );

		$total = 0;
		$this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, $total );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('address') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('address') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('address', 'unknown');
	}
}
