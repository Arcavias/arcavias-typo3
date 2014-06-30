<?php


class Tx_Arcavias_Tests_Unit_Flexform_CatalogTest
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;


	public function setUp()
	{
		$this->_object = new tx_arcavias_flexform_catalog();
	}


	public function tearDown()
	{
		unset( $this->_object );
	}


	/**
	 * @test
	 */
	public function getCategories()
	{
		$result = $this->_object->getCategories( array( 'items' => array() ) );

		$this->assertArrayHasKey( 'items', $result );
	}
}