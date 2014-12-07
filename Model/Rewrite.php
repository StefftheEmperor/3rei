<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 16:27
 */

namespace Model;

class Rewrite extends \Db\AbstractModel {

	protected $primary_key = 'id';
	protected $table_name = 'rewrite';
	public static function factory_by_url(\Url $url = NULL)
	{
		if (isset($url))
		{
			$filter = new \Db\Filter('url', '=', (string)$url);
			$result = \Db\Table::factory('rewrite')->filter($filter)->get_one();

			return $result->map_to(get_called_class());
		}
		else
		{
			$instance = new static;
			$instance->set_table(\Db\Table::factory('rewrite'));
			return $instance;
		}
	}

} 