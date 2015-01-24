<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 14.12.14
 * Time: 17:35
 */

namespace Db;


class AbstractQuery {
	const QUERY_SELECT = 'SELECT';
	const QUERY_DESCRIBE = 'DESCRIBE';
	const QUERY_UPDATE = 'UPDATE';
	const QUERY_INSERT = 'INSERT';
}