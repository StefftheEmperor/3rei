<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 16:27
 */

namespace Request\Model;

class Rewrite extends \Db\Classes\AbstractModel {

	protected $primary_key = 'id';
	protected $table_name = 'rewrite';
	public static function factory_by_url(\Request\Classes\Url $url = NULL)
	{
		if (isset($url))
		{
			$filter = new \Db\Classes\Filter(\Db\Classes\Expression\Row::factory('url'), '=', \Db\Classes\Expression\Value::factory($url->get_absolute_url()));
			$result = \Db\Classes\Table::factory('rewrite')->filter($filter)->get_one();

			return $result->map_to(get_called_class());
		}
		else
		{
			$instance = new static;
			$instance->set_table(\Db\Claasses\Table::factory('rewrite'));
			return $instance;
		}
	}

	public function get_sleeping_request()
	{
		$request = $this->get_request();

		return serialize($this->get_request());
	}
} 