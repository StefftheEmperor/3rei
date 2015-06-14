<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 12:32
 */

namespace Mandator\Model;


class Application extends \Db\Classes\AbstractModel {
	protected $primary_key = 'id';
	protected $table_name = 'request__application';

	public static function factory_by_id($id)
	{
		$application = new static;
		$filter = \Db\Classes\Filter\Comparison::factory(\Db\Classes\Expression\Row::factory($application->get_primary_key()), '=', \Db\Classes\Expression\Value::factory($id));
		$result = \Request\Model\Rewrite\Table::factory($application->get_table_name())->filter($filter)->get_one(\Db\Classes\Table\Select\All::factory());

		return $result->map_to($application);
	}
}