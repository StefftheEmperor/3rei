<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:17
 */

namespace Db\Interfaces;


interface Model {
	public function get_primary_key();
	public function get_table_name();
}