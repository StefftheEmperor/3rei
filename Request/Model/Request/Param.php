<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:28
 */

namespace Request\Model\Request;


use Db\Classes\AbstractModel;
use Db\Classes\Filter\AddAnd;
use Db\Interfaces\Model;
use Request\Interfaces\Request\Attribute;

class Param extends AbstractModel implements Model, Attribute {
	protected $primary_key = 'id';
	protected $table_name = 'request__param';

	public function get_table_name()
	{
		return $this->table_name;
	}

	public static function factory_by_key_value($connection, $key, $value)
	{
		$filter = AddAnd::factory(
			Comparison::factory(\Db\Classes\Expression\Row::factory('key'),\Db\Classes\Expression\Value::factory($key)),
			Comparison::factory(\Db\Classes\Expression\Row::factory('value'),\Db\Classes\Expression\Value::factory($value))
		);
		$result = $connection->get_model_reflection('\Request\Model\Request\Param\Table')->factory($connection, 'request__param')->filter($filter)->get_one(\Db\Classes\Table\Select\All::factory());

		$param = $result->map_to(get_called_class());

		return $param;
	}
}