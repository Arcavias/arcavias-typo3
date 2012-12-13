<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage HTML
 * @version $Id: Default.php 1324 2012-10-21 13:17:19Z nsendetzky $
 */


/**
 * Default implementation of standard basket HTML client.
 *
 * @package Client
 * @subpackage HTML
 */
class Client_Html_Basket_Standard_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/basket/standard/default/subparts';
	private $_subPartNames = array( 'product' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string|null $name Template name
	 * @return string HTML code
	 */
	public function getBody( $name = null )
	{
		$view = $this->_process( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->standardBody = $html;

		$tplconf = 'client/html/basket/standard/default/template-body';
		$default = 'basket/standard/body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string|null $name Template name
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $name = null )
	{
		$view = $this->getView();

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->standardHeader = $html;

		$tplconf = 'client/html/basket/standard/default/template-header';
		$default = 'basket/standard/header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'basket/standard/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return $this->_isCachable( $what, $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _process( MW_View_Interface $view )
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_getContext() );

		switch( $view->param( 'b-action' ) )
		{
			case 'add':
				foreach( $view->param( 'b-prod', array() ) as $values )
				{
					$controller->addProduct(
						( isset( $values['prodid'] ) ? $values['prodid'] : '' ),
						( isset( $values['quantity'] ) ? $values['quantity'] : 1 ),
						( isset( $values['attrconf-id'] ) ? $values['attrconf-id'] : array() ),
						( isset( $values['attrvar-id'] ) ? $values['attrvar-id'] : array() ),
						$view->config( 'basket/require-variant', true )
					);
				}
				break;

			case 'edit':
				foreach( $view->param( 'b-quantity', array() ) as $pos => $qty )
				{
					$controller->editProduct(
						$pos,
						$qty,
						( isset( $values['attrconf-code'] ) ? $values['attrconf-code'] : array() )
					);
				}
				break;

			case 'delete':
				$controller->deleteProduct( $view->param( 'b-position' ) );
				break;
		}

		$view->standardBasket = $controller->get();

		return $view;
	}
}