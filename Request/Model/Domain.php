<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 01.02.15
 * Time: 17:14
 */

namespace Request\Model;


class Domain extends \Db\Classes\AbstractModel implements \Db\Interfaces\Model {
	protected $primary_key = 'id';
	protected $table_name = 'request__domain';

	public function get_table_name()
	{
		return $this->table_name;
	}

	public static function factory_by_host(\Db\Classes\Mysql\Connection $connection, $host)
	{
		$domain = new static($connection);
		$filter = \Db\Classes\Filter\Comparison::factory(\Db\Classes\Expression\Row::factory('host'), \Db\Classes\Expression\Value::factory($host));
		$result = $connection->get_model_reflection('\Request\Model\Rewrite\Table')->factory($domain->get_table_name())->filter($filter)->get_one(\Db\Classes\Table\Select\All::factory());

		return $result->map_to($domain);
	}
}