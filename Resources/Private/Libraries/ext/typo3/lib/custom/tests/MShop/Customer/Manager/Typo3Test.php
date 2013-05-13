<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: Typo3Test.php 15573 2012-04-17 15:22:17Z doleiynyk $
 */

/**
 * Test class for MShop_Customer_Manager_Typo3
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
		$this->_context->getConfig()->set( 'mshop/customer/manager/typo3/pid-default', 999999 );
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
		$search->setConditions( $search->compare( '==', 'customer.code', 'unitCustomer1@metaways.de' ) );
		$items = $this->_object->searchItems( $search );

		if( ( $expected = reset( $items ) ) === false ) {
			throw new Exception( 'No customer found.' );
		}

		$actual = $this->_object->getItem( $expected->getId() );
		$billing = $actual->getBillingAddress();

		$this->assertEquals( $expected, $actual );

		$this->assertEquals( 'Max Mustermann', $actual->getLabel() );
		$this->assertEquals( 'unitCustomer1@metaways.de', $actual->getCode() );
		$this->assertEquals( 'mr', $billing->getSalutation() );
		$this->assertEquals( 'Metaways GmbH', $billing->getCompany() );
		$this->assertEquals( 'Dr.', $billing->getTitle() );
		$this->assertEquals( 'Max', $billing->getFirstname() );
		$this->assertEquals( 'Mustermann', $billing->getLastname() );
		$this->assertEquals( 'Musterstraße 1a', $billing->getAddress1() );
		$this->assertEquals( '', $billing->getAddress2() );
		$this->assertEquals( '', $billing->getAddress3() );
		$this->assertEquals( '20001', $billing->getPostal() );
		$this->assertEquals( 'Musterstadt', $billing->getCity() );
		$this->assertEquals( 'Hamburg', $billing->getState() );
		$this->assertEquals( 'de', $billing->getLanguageId() );
		$this->assertEquals( 'DE', $billing->getCountryId() );
		$this->assertEquals( '01234567890', $billing->getTelephone() );
		$this->assertEquals( 'unitCustomer1@metaways.de', $billing->getEMail() );
		$this->assertEquals( '01234567890', $billing->getTelefax() );
		$this->assertEquals( 'www.metaways.de', $billing->getWebsite() );
		$this->assertEquals( 1, $actual->getStatus() );
		$this->assertEquals( '5f4dcc3b5aa765d61d8327deb882cf99', $actual->getPassword() );
		$this->assertEquals( '2011-01-13 11:03:36', $actual->getTimeCreated() );
		$this->assertEquals( '2011-01-13 11:03:46', $actual->getTimeModified() );
		$this->assertEquals( '', $actual->getEditor() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'unitCustomer1@metaways.de' ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No customer found.' );
		}

		$item->setId( null );
		$item->setCode( 'unitTest' );
		$item->setLabel( 'unitTest' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTest2' );
		$itemExp->setLabel( 'unitTest2' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getBillingAddress(), $itemSaved->getBillingAddress() );
		$this->assertEquals( $item->getBirthday(), $itemSaved->getBirthday() );
		$this->assertEquals( $item->getPassword(), $itemSaved->getPassword() );

		$this->assertEquals( '', $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getBillingAddress(), $itemUpd->getBillingAddress() );
		$this->assertEquals( $itemExp->getBirthday(), $itemUpd->getBirthday() );
		$this->assertEquals( $itemExp->getPassword(), $itemUpd->getPassword() );

		$this->assertEquals( '', $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $item->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'customer.id', null );
		$expr[] = $search->compare( '==', 'customer.label', 'Franz-Xaver Gabler' );
		$expr[] = $search->compare( '==', 'customer.code', 'unitCustomer3@metaways.de' );
		$expr[] = $search->compare( '==', 'customer.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'customer.company', 'Metaways GmbH' );
		$expr[] = $search->compare( '==', 'customer.title', '' );
		$expr[] = $search->compare( '==', 'customer.firstname', 'Franz-Xaver' );
		$expr[] = $search->compare( '==', 'customer.lastname', 'Gabler' );
		$expr[] = $search->compare( '==', 'customer.address1', 'Phantasiestraße 2' );
		$expr[] = $search->compare( '==', 'customer.postal', '23643' );
		$expr[] = $search->compare( '==', 'customer.city', 'Berlin' );
		$expr[] = $search->compare( '==', 'customer.state', 'Berlin' );
		$expr[] = $search->compare( '==', 'customer.telephone', '01234509876' );
		$expr[] = $search->compare( '==', 'customer.email', 'unitCustomer3@metaways.de' );
		$expr[] = $search->compare( '==', 'customer.telefax', '055544333212' );
		$expr[] = $search->compare( '==', 'customer.website', 'www.metaways.de' );
		$expr[] = $search->compare( '==', 'customer.status', 1 );
		$expr[] = $search->compare( '!=', 'customer.password', '' );
		$expr[] = $search->compare( '>', 'customer.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>', 'customer.ctime', '1970-01-01 00:00:00' );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		// search without base criteria
		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems( $search );
		$this->assertEquals( 3, count( $results ) );


		// search with base criteria
		$search = $this->_object->createSearch(true);
		$results = $this->_object->searchItems( $search );
		$this->assertEquals( 2, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getSubManager( 'address', 'unknown' );
	}
}
