<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: typo3.php 15438 2012-03-22 13:23:59Z spopp $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "tt_address"
			WHERE "uid"=?
		',
		'insert' => '
			INSERT INTO "tt_address" ("tx_mshop_siteid", "tx_mshop_fe_user_uid", "company", "gender", "title",
				"first_name","last_name","address","tx_mshop_address2","tx_mshop_address3","zip","city","region",
				"country","tx_mshop_langid","phone","email","fax","www","tx_mshop_pos")
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
		',
		'update' => '
			UPDATE "tt_address"
			SET "tx_mshop_siteid"=?, "tx_mshop_fe_user_uid"=?, "company"=?, "gender"=?, "title"=?, "first_name"=?, "last_name"=?,
				"address"=?, "tx_mshop_address2"=?, "tx_mshop_address3"=?, "zip"=?, "city"=?, "region"=?, "country"=?,
				"tx_mshop_langid"=?, "phone"=?, "email"=?, "fax"=?, "www"=?, "tx_mshop_pos"=?
			WHERE "uid"=?
		',
		'search' => '
			SELECT tt_address."uid" AS "id", tt_address."tx_mshop_siteid" AS "siteid", tt_address."tx_mshop_fe_user_uid" AS "refid", tt_address."company", tt_address."gender" AS "salutation", tt_address."title",
				   tt_address."first_name" AS "firstname", tt_address."last_name" AS "lastname", tt_address."address" AS "address1", tt_address."tx_mshop_address2" AS "address2", tt_address."tx_mshop_address3" AS "address3",
				   tt_address."zip" AS "postal", tt_address."city", tt_address."region" AS "state", tt_address."country" AS "countryid", tt_address."tx_mshop_langid" AS "langid", tt_address."phone" AS "telephone",
				   tt_address."email", tt_address."fax" AS "telefax", tt_address."www" AS "website", tt_address."tx_mshop_pos" AS "pos"
			FROM "tt_address"
			WHERE :cond
				AND ( tt_address."tx_mshop_siteid"=? OR tt_address."tx_mshop_siteid" IS NULL )
			ORDER BY :order
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(DISTINCT tt_address."uid") AS "count"
			FROM "tt_address"
			WHERE :cond
				AND ( tt_address."tx_mshop_siteid"=? OR tt_address."tx_mshop_siteid" IS NULL )
		',
	),
);