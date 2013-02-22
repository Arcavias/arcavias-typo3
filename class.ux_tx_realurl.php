<?php

class ux_tx_realurl extends tx_realurl
{
	static private $_test = 0;

	private $local_cObj = null;


	/**
	 * Creates an instance of this class
	 *
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
	}

	/**
	 * Doing database lookup between "alias values" and "id numbers". Translation is bi-directional.
	 *
	 * @param	array		Configuration of look-up table, field names etc.
	 * @param	string		Value to match field in database to.
	 * @param	boolean		If TRUE, the input $value is an alias-string that needs translation to an ID integer. FALSE (default) means the reverse direction
	 * @return	string		Result value of lookup. If no value was found the $value is returned.
	 */
	protected function lookUpTranslation($cfg, $value, $aliasToUid = FALSE) {
		// Assemble list of fields to look up. This includes localization related fields:
		$langEnabled = FALSE;
		$fieldList = array();
		if ($cfg['languageGetVar'] && $cfg['transOrigPointerField'] && $cfg['languageField']) {
			$fieldList[] = 'uid';
			$fieldList[] = $cfg['transOrigPointerField'];
			$fieldList[] = $cfg['languageField'];
			$langEnabled = TRUE;
		}


		/*
		 * Use stdWrap InsertData to set language into addWhereClause
		 */
		if( $cfg['insertData'] ) {
			$cfg['addWhereClause'] = $this->local_cObj->stdWrap_insertData( $cfg['addWhereClause'] );
		}

		// Translate an alias string to an ID:
		if ($aliasToUid) {

			// First, test if there is an entry in cache for the alias:
			if ($cfg['useUniqueCache'] && $returnId = $this->lookUp_uniqAliasToId($cfg, $value)) {
				return $returnId;
			}
			else { // If no cached entry, look it up directly in the table:

				$fieldList[] = $cfg['id_field'];
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',', $fieldList), $cfg['table'],
									$cfg['alias_field'] . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value, $cfg['table']) .
									' ' . $cfg['addWhereClause']);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
				if ($row) {
					$returnId = $row[$cfg['id_field']];

					// If localization is enabled, check if this record is a localized version and if so, find uid of the original version.
					if ($langEnabled && $row[$cfg['languageField']] > 0) {
						$returnId = $row[$cfg['transOrigPointerField']];
					}

					// Return the id:
					return $returnId;
				}
			}
		} else { // Translate an ID to alias string

			// Define the language for the alias:
			$lang = intval($this->orig_paramKeyValues[$cfg['languageGetVar']]);
			if (t3lib_div::inList($cfg['languageExceptionUids'], $lang)) { // Might be excepted (like you should for CJK cases which does not translate to ASCII equivalents)
				$lang = 0;
			}

			// First, test if there is an entry in cache for the id:
			if ($cfg['useUniqueCache'] && !$cfg['autoUpdate'] && $returnAlias = $this->lookUp_idToUniqAlias($cfg, $value, $lang)) {
				return $returnAlias;
			} else { // If no cached entry, look up alias directly in the table (and possibly store cache value)

				$fieldList[] = $cfg['alias_field'];
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',', $fieldList), $cfg['table'],
							$cfg['id_field'] . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value, $cfg['table']) .
							' ' . $cfg['addWhereClause'] );
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
				if ($row) {

					// Looking for localized version of that:
					if ($langEnabled && $lang) {

						// If the lang value is there, look for a localized version of record:
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($cfg['alias_field'], $cfg['table'],
								$cfg['transOrigPointerField'] . '=' . intval($row['uid']) . '
								AND ' . $cfg['languageField'] . '=' . intval($lang) . '
								' . $cfg['addWhereClause']);
						$lrow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
						$GLOBALS['TYPO3_DB']->sql_free_result($res);
						if ($lrow) {
							$row = $lrow;
						}
					}

					$mLength = $cfg['maxLength'] ? $cfg['maxLength'] : $this->maxLookUpLgd;

					if ($cfg['useUniqueCache']) { // If cache is to be used, store the alias in the cache:
						$aliasBaseValue = $row[$cfg['alias_field']];
						return $this->lookUp_newAlias($cfg, substr($aliasBaseValue, 0, $mLength), $value, $lang);
					} else { // If no cache for alias, then just return whatever value is appropriate:
						if (strlen($row[$cfg['alias_field']]) <= $mLength) {
							return $row[$cfg['alias_field']];
						} else {
							return $value;
						}
					}
				}
			}
		}

		// In case no value was found in translation we return the incoming value. It may be argued that this is not a good idea but generally this can be avoided by using the "useUniqueCache" principle which will ensure unique translation both ways.
		return $value;
	}
}

?>