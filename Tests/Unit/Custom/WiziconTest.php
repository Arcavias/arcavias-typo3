<?php


class Tx_Arcavias_Tests_Unit_Custom_WiziconTest
	extends Tx_Extbase_Tests_Unit_BaseTestCase
{
	private $_object;


	public function setUp()
	{
		$this->_object = new tx_arcavias_custom_wizicon();
	}


	public function tearDown()
	{
		unset( $this->_object );
	}


	/**
	 * @test
	 */
	public function proc()
	{
		$result = $this->_object->proc( array() );

		$this->assertArrayHasKey( 'plugins_tx_arcavias', $result );
		$this->assertArrayHasKey( 'icon', $result['plugins_tx_arcavias'] );
		$this->assertArrayHasKey( 'title', $result['plugins_tx_arcavias'] );
	}
}