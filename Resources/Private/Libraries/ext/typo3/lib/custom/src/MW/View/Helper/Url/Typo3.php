<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 * @version $Id$
 */


/**
 * View helper class for building URLs.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Url_Typo3
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_uriBuilder;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param string $baseUrl URL which acts as base for all constructed URLs
	 */
	public function __construct( $view, Tx_Extbase_MVC_Web_Routing_UriBuilder $uriBuilder )
	{
		parent::__construct( $view );

		$this->_uriBuilder = $uriBuilder;
	}


	/**
	 * Returns the URL assembled from the given arguments.
	 *
	 * @param string|null $target Route or page which should be the target of the link (if any)
	 * @param string|null $controller Name of the controller which should be part of the link (if any)
	 * @param string|null $action Name of the action which should be part of the link (if any)
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param array $trailing Trailing URL parts that are not relevant to identify the resource (for pretty URLs)
	 * @param array $config Additional configuration parameter per URL
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( $target = null, $controller = null, $action = null, array $params = array(), array $trailing = array(), array $config = array() )
	{
		$absoluteUri = ( isset( $config['absoluteUri'] ) && $config['absoluteUri'] == 1 ? true : false );
		$chash = ( isset( $config['chash'] ) && $config['chash'] == 0 ? false : true );

		$this->_uriBuilder->setCreateAbsoluteUri( $absoluteUri );
		$this->_uriBuilder->setTargetPageUid( $target );
		$this->_uriBuilder->setUseCacheHash( $chash );
		$this->_uriBuilder->setArguments( array() ); // remove parameters from previous call

		$uri = $this->_uriBuilder->uriFor( $action, $params, ucfirst( $controller ) );

		$additional = array();

		if( isset( $config['eID'] ) ) {
			$additional[] = 'eID=' . $config['eID'];
		}

		if( isset( $config['type'] ) ) {
			$additional[] = 'type=' . $config['type'];
		}

		if( count( $additional ) > 0 )
		{
			if( strpos( $uri, '?' ) === false ) {
				return $uri . '?' . join( '&', $additional );
			}

			return $uri . '&' . join( '&', $additional );
		}

		return $uri;
	}
}