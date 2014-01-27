<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias base class with common functionality.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Base
{
	static private $_arcavias;
	static private $_config;
	static private $_context;
	static private $_extConfig;


	/**
	 * Creates a new configuration object.
	 *
	 * @param array $local Multi-dimensional associative list with local configuration
	 * @return MW_Config_Interface Configuration object
	 */
	public static function getConfig( array $local = array() )
	{
		if( self::$_config === null )
		{
			$configPaths = self::getArcavias()->getConfigPaths( 'mysql' );

			// Hook for processing extension config directories
			if( is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['confDirs'] ) )
			{
				foreach( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['confDirs'] as $dir )
				{
					$absPath = t3lib_div::getFileAbsFileName( $dir );
					if( !empty( $absPath ) ) {
						$configPaths[] = $absPath;
					}
				}
			}

			$conf = new MW_Config_Array( array(), $configPaths );

			if( function_exists( 'apc_store' ) === true && self::getExtConfig( 'useAPC', false ) == true ) {
				$conf = new MW_Config_Decorator_APC( $conf, self::getExtConfig( 'apcPrefix', 't3:' ) );
			}

			self::$_config = $conf;
		}

		return new MW_Config_Decorator_Memory( self::$_config, $local );
	}


	/**
	 * Returns the Arcavias object.
	 *
	 * @return Arcavias Arcavias object
	 */
	public static function getArcavias()
	{
		if( self::$_arcavias === null )
		{
			$ds = DIRECTORY_SEPARATOR;
			$libPath = t3lib_extMgm::extPath( 'arcavias' ) . 'vendor' . $ds . 'arcavias' . $ds . 'arcavias-core';

			// Hook for processing extension directories
			$extDirs = array();
			if( is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['extDirs'] ) )
			{
				foreach( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['extDirs'] as $dir )
				{
					$absPath = t3lib_div::getFileAbsFileName( $dir );
					if( !empty( $absPath ) ) {
						$extDirs[] = $absPath;
					}
				}
			}

			self::$_arcavias = new Arcavias( $extDirs, false, $libPath );
		}

		return self::$_arcavias;
	}


	/**
	 * Returns the extension configuration.
	 *
	 * @param string Name of the configuration setting
	 * @param mixed Value returned if no value in extension configuration was found
	 * @return mixed Value associated with the configuration setting
	 */
	public static function getExtConfig( $name, $default = null )
	{
		if( self::$_extConfig === null )
		{
			if( ( $conf = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['arcavias'] ) ) === false ) {
				$conf = array();
			}

			self::$_extConfig = $conf;
		}

		if( isset( self::$_extConfig[$name] ) ) {
			return self::$_extConfig[$name];
		}

		return $default;
	}


	/**
	 * Parses TypoScript configuration string.
	 *
	 * @param string $tsString TypoScript string
	 * @return array Mulit-dimensional, associative list of key/value pairs
	 * @throws Exception If parsing the configuration string fails
	 */
	public static function parseTS( $tsString )
	{
		$parser = t3lib_div::makeInstance( 't3lib_tsparser' );
		$parser->parse( $tsString );

		if( !empty( $parser->errors ) )
		{
			$msg = $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.error.tsconfig.invalid' );
			throw new Exception( $msg );
		}

		return self::_convertTypoScriptArrayToPlainArray( $parser->setup );
	}


	/**
	 * Removes dots from config keys (copied from Extbase TypoScriptService class available since TYPO3 6.0)
	 *
	 * @param array $typoScriptArray TypoScript configuration array
	 * @return array Multi-dimensional, associative list of key/value pairs without dots in keys
	 */
	protected static function _convertTypoScriptArrayToPlainArray(array $typoScriptArray)
	{
		foreach ($typoScriptArray as $key => &$value) {
			if (substr($key, -1) === '.') {
				$keyWithoutDot = substr($key, 0, -1);
				$hasNodeWithoutDot = array_key_exists($keyWithoutDot, $typoScriptArray);
				$typoScriptNodeValue = $hasNodeWithoutDot ? $typoScriptArray[$keyWithoutDot] : NULL;
				if (is_array($value)) {
					$typoScriptArray[$keyWithoutDot] = $this->_convertTypoScriptArrayToPlainArray($value);
					if (!is_null($typoScriptNodeValue)) {
						$typoScriptArray[$keyWithoutDot]['_typoScriptNodeValue'] = $typoScriptNodeValue;
					}
					unset($typoScriptArray[$key]);
				} else {
					$typoScriptArray[$keyWithoutDot] = NULL;
				}
			}
		}
		return $typoScriptArray;
	}
}
