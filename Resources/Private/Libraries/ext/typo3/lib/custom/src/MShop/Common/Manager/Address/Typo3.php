<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @package MShop
 * @subpackage Common
 * @version $Id: Typo3.php 16084 2012-07-17 10:23:03Z nsendetzky $
 */


/**
 * Common address manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Manager_Address_Typo3
	extends MShop_Common_Manager_Abstract
	implements MShop_Common_Manager_Address_Interface
{
	private $_context = null;
	private $_config = array();
	private $_searchConfig = array();


	/**
	 * Initializes a new common address manager object using the given context object.
	 *
	 * @param MShop_Context_Interface $_context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context,
		array $config = array( ), array $searchConfig = array( ) )
	{
		$whitelist = array( 'delete', 'insert', 'update', 'search', 'count', 'newid' );
		$isList = array_keys($config);
		foreach ( $whitelist as $str ) {
			if ( !in_array($str, $isList) ) {
				throw new MShop_Exception('No configuration available or missing parts: ' . $str);
			}
		}

		$this->_config = $config;

		parent::__construct($context);

		$this->_context = $context;
		$this->_searchConfig = $searchConfig;

		if ( ( $entry = reset($searchConfig) ) === false ) {
			throw new MShop_Exception('Search configuration is invalid');
		}

		if ( ( $pos = strrpos($entry['code'], '.') ) == false ) {
			throw new MShop_Exception(sprintf('Search configuration for "%1$s" is invalid', $entry['code']));
		}

		if ( ( $this->_prefix = substr($entry['code'], 0, $pos + 1) ) === false ) {
			throw new MShop_Exception(sprintf('Search configuration for "%1$s" is invalid', $entry['code']));
		}
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
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default($fields);
		}

		return $list;
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'common', 'address/' . $manager, $name );
	}


	/**
	 * Instantiates a new common address item object.
	 *
	 * @return MShop_Common_Item_Address_Interface
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_context->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Deletes a common address item object.
	 *
	 * @param integer $id Unique common address ID referencing an existing address
	 */
	public function deleteItem( $id )
	{
		$config = $this->_context->getConfig();
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$sql = $this->_config['delete'];

			$statement = $conn->create($sql);
			$statement->bind(1, $id, MW_DB_Statement_Abstract::PARAM_INT);
			$result = $statement->execute()->finish();
			$dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$dbm->release($conn);
			throw $e;
		}
	}


	/**
	 * Returns the common address item object specificed by its ID.
	 *
	 * @param integer $id Unique common address ID referencing an existing address
	 */
	public function getItem( $id, array $ref = array() )
	{
		$conf = reset( $this->_searchConfig );

		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', $conf['code'], $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if ( ( $item = reset( $items ) ) === false ) {
			throw new MShop_Exception( sprintf( 'No item for ID "%1$d" found', $id ) );
		}

		return $item;
	}


	/**
	 * Saves a common address item object.
	 *
	 * @param MShop_Common_Item_Address_Interface $item common address item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Common_Item_Address_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Exception( sprintf( 'Object does not implement "%1$s"', $iface ) );
		}

		$config = $this->_context->getConfig();
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			if ( $item->getId() === null ) {
				$sql = $this->_config['insert'];
			} else {
				$sql = $this->_config['update'];
			}

			$stmt = $conn->create($sql);

			$stmt->bind(1, $this->_context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(2, $item->getRefId(), MW_DB_Statement_Abstract::PARAM_STR); //reference id
			$stmt->bind(3, $item->getCompany(), MW_DB_Statement_Abstract::PARAM_STR); //company
			$stmt->bind(4, $this->_translateSalutation( $item->getSalutation(), true ), MW_DB_Statement_Abstract::PARAM_STR); //salutation
			$stmt->bind(5, $item->getTitle(), MW_DB_Statement_Abstract::PARAM_STR); //title
			$stmt->bind(6, $item->getFirstname(), MW_DB_Statement_Abstract::PARAM_STR); //firstname
			$stmt->bind(7, $item->getLastname(), MW_DB_Statement_Abstract::PARAM_STR); //lastname
			$stmt->bind(8, $item->getAddress1(), MW_DB_Statement_Abstract::PARAM_STR); //address1
			$stmt->bind(9, $item->getAddress2(), MW_DB_Statement_Abstract::PARAM_STR); //address2
			$stmt->bind(10, $item->getAddress3(), MW_DB_Statement_Abstract::PARAM_STR); //address3
			$stmt->bind(11, $item->getPostal(), MW_DB_Statement_Abstract::PARAM_STR); //postal
			$stmt->bind(12, $item->getCity(), MW_DB_Statement_Abstract::PARAM_STR); //city
			$stmt->bind(13, $item->getState(), MW_DB_Statement_Abstract::PARAM_STR); //state
			$stmt->bind(14, $item->getCountryId(), MW_DB_Statement_Abstract::PARAM_STR); //countryid
			$stmt->bind(15, $item->getLanguageId(), MW_DB_Statement_Abstract::PARAM_STR); //langid
			$stmt->bind(16, $item->getTelephone(), MW_DB_Statement_Abstract::PARAM_STR); //telephone
			$stmt->bind(17, $item->getEmail(), MW_DB_Statement_Abstract::PARAM_STR); //email
			$stmt->bind(18, $item->getTelefax(), MW_DB_Statement_Abstract::PARAM_STR); //telefax
			$stmt->bind(19, $item->getWebsite(), MW_DB_Statement_Abstract::PARAM_STR); //website
			$stmt->bind(20, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT); //position

			if ( $item->getId() !== null ) {
				$stmt->bind(21, $item->getId(), MW_DB_Statement_Abstract::PARAM_INT);
				$item->setId($item->getId()); //is not modified anymore
			}

			$result = $stmt->execute()->finish();

			if ( $item->getId() === null ) {
				$item->setId($this->_newId($conn, $this->_config['newid']));
			}

			$dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$dbm->release($conn);
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Address_Interface
	 * @throws MShop_Exception If creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$config = $this->_context->getConfig();
		$dbm = $this->_context->getDatabaseManager();
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

			$sql = $this->_config['search'];
			$results = $this->_getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

			try
			{
				while ( ( $row = $results->fetch() ) !== false ) {
					$items[ $row['id'] ] = $this->_createItem( $row );
				}
			}
			catch ( Exception $e )
			{
				$results->finish();
				throw $e;
			}

			if ( $total !== null )
			{
				$sql = $this->_config['count'];
				$results = $this->_getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

				$row = $results->fetch();
				$results->finish();

				if ( $row === false ) {
					throw new MShop_Exception( 'No total results value found.' );
				}

				$total = $row['count'];
			}
			$dbm->release( $conn );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $items;
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
		$statement->bind(1, $this->_context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
		$this->_context->getLogger()->log(__METHOD__ . ': SQL statement: ' . $statement, MW_Logger_Abstract::DEBUG);

		$results = $statement->execute();

		return $results;
	}


	/**
	 * Creates a new address item
	 *
	 * @param array $values List of attributes for address item
	 * @return MShop_Common_Item_Address_Interface New address item
	 */
	protected function _createItem( array $values = array( ) )
	{
		if( array_key_exists('salutation', $values )) {
			$values['salutation'] = $this->_translateSalutation( $values['salutation'] );
		}
		return new MShop_Common_Item_Address_Default($this->_prefix, $values);
	}


	/**
	 * Map salutation values for using table tt_address in Typo3
	 *
	 * @param string $value Salutation value to translate
	 * @param boolean $reverse If true, then translates for saveItem, otherwise for creaetItem
	 * @return string Translates value
	 */
	protected function _translateSalutation( $value, $reverse = false )
	{
		if( $reverse ) {
			switch( strtolower($value) )
			{
				case MShop_Common_Item_Address_Abstract::SALUTATION_MRS:
				case MShop_Common_Item_Address_Abstract::SALUTATION_MISS:
					return 'f';
				default:
					return 'm';
			}
		}

		switch( strtolower( $value ) )
		{
			case 'm':
				return MShop_Common_Item_Address_Abstract::SALUTATION_MR;
			case 'f':
				return MShop_Common_Item_Address_Abstract::SALUTATION_MRS;
			default:
				return MShop_Common_Item_Address_Abstract::SALUTATION_UNKNOWN;
		}
	}
}
