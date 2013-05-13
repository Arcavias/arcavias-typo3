<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "fe_users"
			WHERE "uid" = ?
		',
		'insert' => '
			INSERT INTO "fe_users" ("name", "username", "gender", "company", "title", "first_name", "last_name",
				"address", "zip", "city", "zone", "language", "telephone", "email",
				"fax", "www", "date_of_birth", "disable", "password", "tstamp", "static_info_country", "crdate", "pid")
			SELECT ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,( SELECT "cn_iso_3" FROM "static_countries" WHERE "cn_iso_2"=? LIMIT 1 ),?,? FROM DUAL
		',
		'update' => '
			UPDATE "fe_users"
			SET "name"=?, "username"=?, "gender"=?, "company"=?, "title"=?, "first_name"=?, "last_name"=?,
				"address"=?, "zip"=?, "city"=?, "zone"=?, "language"=?, "telephone"=?, "email"=?,
				"fax"=?, "www"=?, "date_of_birth"=?, "disable"=?, "password"=?, "tstamp"=?,
				"static_info_country"=( SELECT "cn_iso_3" FROM "static_countries" WHERE "cn_iso_2"=? LIMIT 1 )
			WHERE "uid"=?
		',
		'search' => '
			SELECT DISTINCT tfeu."uid" AS "id", tfeu."name" AS "label", tfeu."username" AS "code", tfeu."gender",
				tfeu."company", tfeu."title", tfeu."first_name" AS "firstname", tfeu."last_name" AS "lastname",
				tfeu."address" AS "address1", tfeu."zip" AS "postal", tfeu."city", tfeu."zone" AS "state",
				tfeu."language" AS "langid", tsc."cn_iso_2" AS "countryid", tfeu."telephone", tfeu."email",
				tfeu."fax" AS "telefax", tfeu."www" AS "website", tfeu."date_of_birth", tfeu."disable", tfeu."password",
				tfeu."crdate", tfeu."tstamp"
			FROM "fe_users" as tfeu
			LEFT JOIN "static_countries" AS tsc ON tfeu."static_info_country" = tsc."cn_iso_3"
			:joins
			WHERE :cond
				AND tfeu."deleted" = 0
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT tfeu."uid"
				FROM "fe_users" AS tfeu
				LEFT JOIN "static_countries" AS tsc ON tfeu."static_info_country" = tsc."cn_iso_3"
				:joins
				WHERE :cond
					AND tfeu."deleted" = 0
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);