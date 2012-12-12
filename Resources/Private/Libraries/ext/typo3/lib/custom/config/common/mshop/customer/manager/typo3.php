<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: typo3.php 15438 2012-03-22 13:23:59Z spopp $
 */

return array(
	'item' => array(
		'search' => '
			SELECT DISTINCT tfeu."uid" AS "id", tfeu."name", tfeu."username", tfeu."disable"
			FROM "fe_users" as tfeu
			LEFT JOIN "tt_address" ON( tfeu."uid" = tt_address."tx_mshop_fe_user_uid" )
			WHERE
				:cond
				AND tfeu."deleted" = 0
				AND tt_address."deleted" = 0
				AND tt_address."hidden" = 0
			ORDER BY :order
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(DISTINCT tfeu."uid") AS "count"
			FROM "fe_users" AS tfeu
			LEFT JOIN "tt_address" ON( tfeu."uid" = tt_address."tx_mshop_fe_user_uid" )
			WHERE
				:cond
				AND tfeu."deleted"=0
				AND tt_address."deleted" = 0
				AND tt_address."hidden" = 0
		',
	),
);