<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 12:32
 */

namespace Mandator\Model\Application;


class Instance extends \Db\Classes\AbstractModel {
	protected $primary_key = 'id';
	protected $table_name = 'request__application__instance';

	protected static $request_domain2applcation_instance_table = 'request__domain2request__application__instance';

	protected $application = NULL;
	public static function factory_by_domain(\Db\Classes\Mysql\Connection $connection, \Request\Model\Domain $domain)
	{
		$application_instance = new static($connection);

		if ( ! ($domain->is_new()))
		{
			$domain_filter = \Db\Classes\Filter\Comparison::factory(\Db\Classes\Expression\Row::factory('domain_id'), \Db\Classes\Expression\Value::factory($domain->get_primary_id()));
			$result = \Request\Model\Rewrite\Table::factory($connection, static::$request_domain2applcation_instance_table)->filter($domain_filter)->get_one(\Db\Classes\Table\Select\All::factory());

			if (isset($result->application_instance_id))
			{
				$application_instance_filter = \Db\Classes\Filter\Comparison::factory(\Db\Classes\Expression\Row::factory($application_instance->get_primary_key()), \Db\Classes\Expression\Value::factory($result->application_instance_id));
				$result = \Request\Model\Rewrite\Table::factory($connection, $application_instance->get_table_name())->filter($application_instance_filter)->get_one(\Db\Classes\Table\Select\All::factory());

				$application_instance = $result->map_to($application_instance);
			}
		}
		return $application_instance;
	}

	public function get_application()
	{
		if ( ! isset($this->application))
		{
			$this->application = \Mandator\Model\Application::factory_by_id($this->get_application_id());
		}

		return $this->application;
	}

	public function get_table_name()
	{
		return $this->table_name;
	}
}