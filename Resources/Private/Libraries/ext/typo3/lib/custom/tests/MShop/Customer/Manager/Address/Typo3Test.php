<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: Typo3Test.php 16084 2012-07-17 10:23:03Z nsendetzky $
 */


/**
 * Test class for MShop_Common_Manager_Address_Default
 */
class MShop_Customer_Manager_Address_Typo3Test extends MW_Unittest_Testcase
{
	/**
	 *
	 * @var MShop_Common_Manager_Address_Default
	 */
	protected $_object = null;

	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Customer_Manager_Address_Typo3Test');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( TestHelper::getContext(), 'Typo3' );
		$this->_object = $customerManager->getSubManager('address', 'Typo3' );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->_object);
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
		$this->assertInstanceOf( 'MShop_Common_Item_Address_Interface', $item );
	}

	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '~=', 'customer.address.company', 'Metaways' ) );

		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No address item with company "Metaways" found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}

	public function testSaveUpdateAndDeleteItem()
	{
		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No address item found' );
		}

		$item->setId( null );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setCity( 'Berlin' );
		$itemExp->setState( 'Berlin' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );

		$this->assertEquals( $item->getCity(), $itemSaved->getCity());
		$this->assertEquals( $item->getState(), $itemSaved->getState());

		$this->assertEquals( $itemExp->getCity(), $itemUpd->getCity());
		$this->assertEquals( $itemExp->getState(), $itemUpd->getState());

		$this->setExpectedException('MShop_Exception');
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

		$conditions = array();
		$conditions[] = $search->compare( '!=', 'customer.address.id', null );
		$conditions[] = $search->compare( '!=', 'customer.address.siteid', null );
		$conditions[] = $search->compare( '!=', 'customer.address.refid', null );
		$conditions[] = $search->compare( '==', 'customer.address.company', 'Metaways GmbH' );
		$conditions[] = $search->compare( '==', 'customer.address.salutation', MShop_Common_Item_Address_Abstract::SALUTATION_MR );
		$conditions[] = $search->compare( '==', 'customer.address.title', 'Dr.' );
		$conditions[] = $search->compare( '==', 'customer.address.firstname', 'Max' );
		$conditions[] = $search->compare( '==', 'customer.address.lastname', 'Mustermann' );
		$conditions[] = $search->compare( '==', 'customer.address.address1', 'Musterstraße' );
		$conditions[] = $search->compare( '==', 'customer.address.address2', '1a' );
		$conditions[] = $search->compare( '==', 'customer.address.address3', null );
		$conditions[] = $search->compare( '==', 'customer.address.postal', '20001' );
		$conditions[] = $search->compare( '==', 'customer.address.city', 'Musterstadt' );
		$conditions[] = $search->compare( '==', 'customer.address.state', 'HH' );
		$conditions[] = $search->compare( '==', 'customer.address.countryid', 'DE' );
		$conditions[] = $search->compare( '==', 'customer.address.telephone', '01234567890' );
		$conditions[] = $search->compare( '==', 'customer.address.email', 'arcavias@metaways.de' );
		$conditions[] = $search->compare( '==', 'customer.address.telefax', '01234567890' );
		$conditions[] = $search->compare( '==', 'customer.address.website', 'www.metaways.de' );

		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);
		$result = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		foreach($result as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}
