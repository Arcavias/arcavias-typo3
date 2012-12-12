<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @package MShop
 * @subpackage Customer
 * @version $Id: Typo3.php 15921 2012-07-02 15:08:25Z nsendetzky $
 */


/**
 * Typo3 implementation of the customer class.
 *
 * @package MShop
 * @subpackage Customer
 */
class MShop_Customer_Manager_Typo3 extends MShop_Customer_Manager_Default
{
	private $_searchConfig = array(
		'customer.id' => array(
			'label' => 'Customer ID',
			'code' => 'customer.id',
			'internalcode' => 'tfeu."uid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
		'customer.label' => array(
			'label' => 'Customer name',
			'code' => 'customer.label',
			'internalcode' => 'tfeu."name"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.code' => array(
			'label' => 'Customer username',
			'code' => 'customer.code',
			'internalcode' => 'tfeu."username"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.status'=> array(
			'label' => 'Customer status',
			'code' => 'customer.status',
			'internalcode' => 'NOT tfeu."disable"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
	);

	private $_addressSearchConfig = array(
		'customer.address.id' => array(
			'label' => 'Customer address ID',
			'code' => 'customer.address.id',
			'internalcode' => 'tt_address."uid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
		'customer.address.siteid' => array(
			'label' => 'Customer address site ID',
			'code' => 'customer.address.siteid',
			'internalcode' => 'tt_address."tx_mshop_siteid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
		'customer.address.refid' => array(
			'label' => 'Customer address reference ID',
			'code' => 'customer.address.refid',
			'internalcode' => 'tt_address."tx_mshop_fe_user_uid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.company'=> array(
			'label' => 'Customer address company',
			'code' => 'customer.address.company',
			'internalcode' => 'tt_address."company"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.salutation' => array(
			'label' => 'Customer address salutation',
			'code' => 'customer.address.salutation',
			'internalcode' => 'tt_address."gender"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.title' => array(
			'label' => 'Customer address title',
			'code' => 'customer.address.title',
			'internalcode' => 'tt_address."title"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.firstname' => array(
			'label' => 'Customer address firstname',
			'code' => 'customer.address.firstname',
			'internalcode' => 'tt_address."first_name"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.lastname' => array(
			'label' => 'Customer address lastname',
			'code' => 'customer.address.lastname',
			'internalcode' => 'tt_address."last_name"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.address1' => array(
			'label' => 'Customer address address part one',
			'code' => 'customer.address.address1',
			'internalcode' => 'tt_address."address"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.address2' => array(
			'label' => 'Customer address address part two',
			'code' => 'customer.address.address2',
			'internalcode' => 'tt_address."tx_mshop_address2"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.address3' => array(
			'label' => 'Customer address address part three',
			'code' => 'customer.address.address3',
			'internalcode' => 'tt_address."tx_mshop_address3"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.postal' => array(
			'label' => 'Customer address postal',
			'code' => 'customer.address.postal',
			'internalcode' => 'tt_address."zip"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.city' => array(
			'label' => 'Customer address city',
			'code' => 'customer.address.city',
			'internalcode' => 'tt_address."city"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.state' => array(
			'label' => 'Customer address state',
			'code' => 'customer.address.state',
			'internalcode' => 'tt_address."region"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.countryid' => array(
			'label' => 'Customer address country',
			'code' => 'customer.address.countryid',
			'internalcode' => 'tt_address."country"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.telephone' => array(
			'label' => 'Customer address telephone',
			'code' => 'customer.address.telephone',
			'internalcode' => 'tt_address."phone"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.email' => array(
			'label' => 'Customer address email',
			'code' => 'customer.address.email',
			'internalcode' => 'tt_address."email"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.telefax' => array(
			'label' => 'Customer address telefax',
			'code' => 'customer.address.telefax',
			'internalcode' => 'tt_address."fax"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.website' => array(
			'label' => 'Customer address website',
			'code' => 'customer.address.website',
			'internalcode' => 'tt_address."www"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		'customer.address.flag' => array(
			'label' => 'Customer address flag',
			'code' => 'customer.address.flag',
			'internalcode' => 'tfeuad."flag"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
	);


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$config = $this->_getContext()->getConfig();

			foreach( $config->get( 'classes/customer/manager/submanagers', array( 'address' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}

			foreach( $config->get( 'classes/customer/manager/refmanagers', array() ) as $domain ) {
				$list = array_merge( $list, $this->_createDomainManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Instantiates a new customer item object.
	 *
	 * @return MShop_Customer_Item_Interface New customer item object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		$addressManager = $this->getSubManager( 'address' );
		$address = $addressManager->createItem();
		return $this->_createItem( $address, $values );
	}


	/**
	 * Deletes a customer item object from the permanent storage.
	 * This method is not available for this manager and
	 * will throw an exception if called because the objects are read-only
	 *
	 * @param integer $id Unique customer ID referencing an existing customer
	 */
	public function deleteItem($id)
	{
		throw new MShop_Customer_Exception( 'Item is read only.' );
	}


	/**
	 * Saves a customer item object.
	 *
	 * @param MShop_Customer_Item_Interface $item Customer item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		throw new MShop_Customer_Exception( "Item is read only." );
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Customer_Item_Interface
	 * @throws MShop_Customer_Exception If creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		$items = array();

		try
		{
			$attributes = $this->getSearchAttributes();
			$types = $this->_getSearchTypes( $attributes );
			$translations = $this->_getSearchTranslations( $attributes );

			$plugins = array(
				'customer.address.salutation' => new MShop_Common_Manager_Address_Plugin_Typo3(),
			);

			$find = array( ':cond', ':order', ':start', ':size' );
			$replace = array(
				$search->getConditionString( $types, $translations, $plugins ),
				$search->getSortationString( $types, $translations ),
				$search->getSliceStart(),
				$search->getSliceSize(),
			);


			if( $total !== null )
			{
				$sql = $context->getConfig()->get( 'mshop/customer/manager/typo3/item/count',
					'mshop/customer/manager/typo3/item/count' );
				$results = $this->_getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

				$row = $results->fetch();
				$results->finish();

				if ( $row === false ) {
					throw new MShop_Customer_Exception( 'No total results value found' );
				}

				$total = $row['count'];
			}


			$path = 'mshop/customer/manager/typo3/item/search';
			$sql = $context->getConfig()->get( $path, $path );
			$results = $this->_getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

			$addressManager = $this->getSubManager( 'address' );
			$addressItem = $addressManager->createItem();

			while( ( $row = $results->fetch() ) !== false )
			{
				$items[ $row['id'] ] = $this->_createItem( $addressItem, $row );
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns a new manager for customer extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		if( $manager === 'address' )
		{
				$path = 'mshop/customer/manager/address/typo3/item';
				return $this->_createAddressManager( 'customer', $name, $path, $this->_addressSearchConfig );
		}

		return parent::getSubManager( $manager, $name );
	}


	/**
	 * Returns the search results for the given SQL statement.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param $sql SQL statement
	 * @return MW_DB_Result_Interface Search result object
	 */
	protected function _getSearchResults( MW_DB_Connection_Interface $conn, $sql )
	{
		$statement = $conn->create($sql);

		$this->_getContext()->getLogger()->log( __METHOD__ . ': SQL statement: ' . $statement, MW_Logger_Abstract::DEBUG );

		$results = $statement->execute();

		return $results;
	}


	/**
	 * Creates a new customer item.
	 *
	 * @param array $values List of attributes for customer item
	 * @return MShop_Customer_Item_Interface New customer item
	 */
	protected function _createItem( MShop_Common_Item_Address_Interface $address, array $values = array(),
		array $listItems = array(), array $refItems = array() )
	{
		if( array_key_exists( 'disable', $values ) ) {
			$values['status'] = ( $values['disable'] ? 0 : 1 );
		}

		$values['siteid'] = $this->_getContext()->getLocale()->getSiteId();
		return new MShop_Customer_Item_Default( $address, $values, $listItems, $refItems );
	}

}