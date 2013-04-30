<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MW_Common_Criteria_Plugin_T3Salutation
 */
class MW_Common_Criteria_Plugin_T3SalutationTest extends MW_Unittest_Testcase
{
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MW_Common_Criteria_Plugin_T3SalutationTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_object = new MW_Common_Criteria_Plugin_T3Salutation();
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}


	public function testTranslate()
	{
		$this->assertEquals( 99, $this->_object->translate( MShop_Common_Item_Address_Abstract::SALUTATION_UNKNOWN ) );
	}


	public function testTranslateCompany()
	{
		$this->assertEquals( 99, $this->_object->translate( MShop_Common_Item_Address_Abstract::SALUTATION_COMPANY ) );
	}


	public function testTranslateMale()
	{
		$this->assertEquals( 0, $this->_object->translate( MShop_Common_Item_Address_Abstract::SALUTATION_MR ) );
	}


	public function testTranslateFemale()
	{
		$this->assertEquals( 1, $this->_object->translate( MShop_Common_Item_Address_Abstract::SALUTATION_MRS ) );
		$this->assertEquals( 1, $this->_object->translate( MShop_Common_Item_Address_Abstract::SALUTATION_MISS ) );
	}


	public function testReverse()
	{
		$this->assertEquals( MShop_Common_Item_Address_Abstract::SALUTATION_UNKNOWN, $this->_object->reverse( 99 ) );
	}


	public function testReverseMale()
	{
		$this->assertEquals( MShop_Common_Item_Address_Abstract::SALUTATION_MR, $this->_object->reverse( 0 ) );
	}


	public function testReverseFemale()
	{
		$this->assertEquals( MShop_Common_Item_Address_Abstract::SALUTATION_MRS, $this->_object->reverse( 1 ) );
	}
}
