<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 18:03
 */

namespace Model;


class Permission extends \AbstractModel {

	public static function factory_by_user_id($user_id)
	{
		$filter = new \Db\Classes\Filter('user_id', '=', $user_id);
		$result = \Db\Classes\Table::factory('permission')->filter($filter)->get_all();

		return $result->map_to(get_called_class());
	}
} 