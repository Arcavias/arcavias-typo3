<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Customer
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
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT
		),
		'customer.label' => array(
			'label' => 'Customer name',
			'code' => 'customer.label',
			'internalcode' => 'tfeu."name"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.code' => array(
			'label' => 'Customer username',
			'code' => 'customer.code',
			'internalcode' => 'tfeu."username"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.salutation' => array(
			'label' => 'Customer salutation',
			'code' => 'customer.salutation',
			'internalcode' => 'tfeu."gender"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.company'=> array(
			'label' => 'Customer company',
			'code' => 'customer.company',
			'internalcode' => 'tfeu."company"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.title' => array(
			'label' => 'Customer title',
			'code' => 'customer.title',
			'internalcode' => 'tfeu."title"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.firstname' => array(
			'label' => 'Customer firstname',
			'code' => 'customer.firstname',
			'internalcode' => 'tfeu."first_name"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.lastname' => array(
			'label' => 'Customer lastname',
			'code' => 'customer.lastname',
			'internalcode' => 'tfeu."last_name"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address1' => array(
			'label' => 'Customer address part one',
			'code' => 'customer.address1',
			'internalcode' => 'tfeu."address"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address2' => array(
			'label' => 'Customer address part two',
			'code' => 'customer.address2',
			'internalcode' => 'tfeu."address"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address3' => array(
			'label' => 'Customer address part three',
			'code' => 'customer.address3',
			'internalcode' => 'tfeu."address"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.postal' => array(
			'label' => 'Customer postal',
			'code' => 'customer.postal',
			'internalcode' => 'tfeu."zip"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.city' => array(
			'label' => 'Customer city',
			'code' => 'customer.city',
			'internalcode' => 'tfeu."city"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.state' => array(
			'label' => 'Customer state',
			'code' => 'customer.state',
			'internalcode' => 'tfeu."zone"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.languageid' => array(
			'label' => 'Customer language',
			'code' => 'customer.languageid',
			'internalcode' => 'tfeu."language"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.countryid' => array(
			'label' => 'Customer country',
			'code' => 'customer.countryid',
			'internalcode' => 'tsc."cn_iso_2"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.telephone' => array(
			'label' => 'Customer telephone',
			'code' => 'customer.telephone',
			'internalcode' => 'tfeu."telephone"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.email' => array(
			'label' => 'Customer email',
			'code' => 'customer.email',
			'internalcode' => 'tfeu."email"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.telefax' => array(
			'label' => 'Customer telefax',
			'code' => 'customer.telefax',
			'internalcode' => 'tfeu."fax"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.website' => array(
			'label' => 'Customer website',
			'code' => 'customer.website',
			'internalcode' => 'tfeu."www"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.birthday' => array(
			'label' => 'Customer birthday',
			'code' => 'customer.birthday',
			'internalcode' => 'tfeu."date_of_birth"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.password'=> array(
			'label' => 'Customer password',
			'code' => 'customer.password',
			'internalcode' => 'tfeu."password"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.status'=> array(
			'label' => 'Customer status',
			'code' => 'customer.status',
			'internalcode' => 'tfeu."disable"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT
		),
		'customer.ctime'=> array(
			'label' => 'Customer creation time',
			'code' => 'customer.ctime',
			'internalcode' => 'tfeu."crdate"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.mtime'=> array(
			'label' => 'Customer modification time',
			'code' => 'customer.mtime',
			'internalcode' => 'tfeu."tstamp"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		// not available
		'customer.editor'=> array(
			'label'=>'Customer editor',
			'code'=>'customer.editor',
			'internalcode'=>'1',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_plugins = array();
	private $_reverse = array();
	private $_pid;



	/**
	 * Initializes a new customer manager object using the given context object.
	 *
	 * @param MShop_Context_Interface $_context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$plugin = new MW_Common_Criteria_Plugin_T3Salutation();
		$this->_plugins['customer.salutation'] = $this->_reverse['gender'] = $plugin;

		$plugin = new MW_Common_Criteria_Plugin_T3Status();
		$this->_plugins['customer.status'] = $this->_reverse['disable'] = $plugin;

		$plugin = new MW_Common_Criteria_Plugin_T3Date();
		$this->_plugins['customer.birthday'] = $this->_reverse['date_of_birth'] = $plugin;

		$plugin = new MW_Common_Criteria_Plugin_T3Datetime();
		$this->_plugins['customer.ctime'] = $this->_reverse['crdate'] = $plugin;
		$this->_plugins['customer.mtime'] = $this->_reverse['tstamp'] = $plugin;

		$this->_pid = $context->getConfig()->get( 'mshop/customer/manager/typo3/pid-default', 0 );
	}


	/**
	 * Creates a criteria object for searching.
	 *
	 * @param boolean $default Include default criteria like the status
	 * @return MW_Common_Criteria_Interface Search criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$dbm = $this->_getContext()->getDatabaseManager();
			$conn = $dbm->acquire();

			$object = new MW_Common_Criteria_SQL( $conn );
			$object->setConditions( $object->compare( '==', 'customer.status', 1 ) );

			$dbm->release( $conn );

			return $object;
		}

		return parent::createSearch();
	}


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

			foreach( $config->get( 'classes/customer/manager/submanagers', array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
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
		return $this->_createItem( new MShop_Common_Item_Address_Default( 'customer' ) );
	}


	/**
	 * Deletes a customer item object from the permanent storage.
	 *
	 * @param integer $id Unique customer ID referencing an existing customer
	 */
	public function deleteItem($id)
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement($conn, 'mshop/customer/manager/typo3/item/delete');
			$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Saves a customer item object.
	 *
	 * @param MShop_Customer_Item_Interface $item Customer item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Customer_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Customer_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/customer/manager/typo3/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$sql = $config->get( $path, $path );

			$stmt = $conn->create( $sql );
			$billingAddress = $item->getBillingAddress();

			$addressParts = ( ( $part = $billingAddress->getAddress2() ) != '' ? ' ' . $part : '' );
			$addressParts .= ( ( $part = $billingAddress->getAddress3() ) != '' ? ' ' . $part : '' );

			// TYPO3 fe_users.static_info_country is a three letter ISO code instead a two letter one
			$stmt->bind( 1, $item->getLabel() );
			$stmt->bind( 2, $item->getCode() );
			$stmt->bind( 3, $this->_plugins['customer.salutation']->translate( $billingAddress->getSalutation() ), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $billingAddress->getCompany() );
			$stmt->bind( 5, $billingAddress->getTitle() );
			$stmt->bind( 6, $billingAddress->getFirstname() );
			$stmt->bind( 7, $billingAddress->getLastname() );
			$stmt->bind( 8, $billingAddress->getAddress1() . $addressParts );
			$stmt->bind( 9, $billingAddress->getPostal() );
			$stmt->bind( 10, $billingAddress->getCity() );
			$stmt->bind( 11, $billingAddress->getState() );
			$stmt->bind( 12, $billingAddress->getLanguageId() );
			$stmt->bind( 13, $billingAddress->getTelephone() );
			$stmt->bind( 14, $billingAddress->getEmail() );
			$stmt->bind( 15, $billingAddress->getTelefax() );
			$stmt->bind( 16, $billingAddress->getWebsite() );
			$stmt->bind( 17, $this->_plugins['customer.birthday']->translate( $item->getBirthday() ), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 18, $this->_plugins['customer.status']->translate( $item->getStatus() ), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 19, $item->getPassword() );
			$stmt->bind( 20, time(), MW_DB_Statement_Abstract::PARAM_INT ); // Modification time
			$stmt->bind( 21, $billingAddress->getCountryId() );

			if( $id !== null ) {
				$stmt->bind( 22, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 22, time() ); // Creation time
				$stmt->bind( 23, $this->_pid ); // TYPO3 PID value
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/customer/manager/typo3/item/newid';
					$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
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
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();
		$map = array();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/customer/manager/typo3/item/search';
			$cfgPathCount = 'mshop/customer/manager/typo3/item/count';
			$required = array( 'customer' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level, $this->_plugins );
			while( ( $row = $results->fetch() ) !== false ) {
				$map[ $row['id'] ] = $row;
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $this->_buildItems( $map, $ref, 'customer' );
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
		return parent::getSubManager( $manager, $name );
	}


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param array $domains List of domain names whose referenced items should be attached
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	protected function _buildItems( array $map, array $domains, $prefix )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = array();

		foreach ( $map as $id => $values )
		{
			$listItems = array();
			if ( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = array();
			if ( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			// Hand over empty address item, which will be filled in the customer item constructor
			$items[ $id ] = $this->_createItem( new MShop_Common_Item_Address_Default( $prefix ), $values, $listItems, $refItems );
		}

		return $items;
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
		$values['siteid'] = $this->_getContext()->getLocale()->getSiteId();

		if( array_key_exists( 'date_of_birth', $values ) ) {
			$values['birthday'] = $this->_reverse['date_of_birth']->reverse( $values['date_of_birth'] );
		}

		if( array_key_exists( 'gender', $values ) ) {
			$values['salutation'] = $this->_reverse['gender']->reverse( $values['gender'] );
		}

		if( array_key_exists( 'disable', $values ) ) {
			$values['status'] = $this->_reverse['disable']->reverse( $values['disable'] );
		}

		if( array_key_exists( 'tstamp', $values ) ) {
			$values['mtime'] = $this->_reverse['tstamp']->reverse( $values['tstamp'] );
		}

		if( array_key_exists( 'crdate', $values ) ) {
			$values['ctime'] = $this->_reverse['crdate']->reverse( $values['crdate'] );
		}

		if( array_key_exists( 'langid', $values ) ) {
			$values['langid'] = strtolower( $values['langid'] );
		}

		return new MShop_Customer_Item_Default( $address, $values, $listItems, $refItems );
	}

}