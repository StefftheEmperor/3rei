<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:44
 */

namespace Db;

class Result extends \AbstractModel {

	protected $statement = NULL;

	public function __construct($statement)
	{
		$this->statement = $statement;
	}

	public function get_statement()
	{
		return $this->statement;
	}

	public function get_query()
	{
		return $this->get_statement()->get_query();
	}

	public function map_to($classname)
	{
		$reflection_class = new \ReflectionClass($classname);

		$instance = $reflection_class->newInstance();

		$primary_id = $instance->get_primary_id();
		if (isset($primary_id))
		{
			$instance->is_new(FALSE);
		}
		if ($this->get_query()->is_insert())
		{
			$instance->set_primary_id($this->get_statement()->get_conenction()->lastInsertId());
		}
		foreach ($this->get_query()->get_filter() as $filter)
		{
			if ($filter->get_operator() === '=') {
				$instance->{'set_' . strtolower($filter->get_operand1())}($filter->get_operand2());
			}
		}
		foreach ($this as $key => $value)
		{
			$instance->{'set_'.strtolower($key)}($value);

			if ($key == $instance->get_table()->get_primary_key())
			{
				$instance->is_new(FALSE);
			}
		}

		foreach ($this->get_available_keys() as $key)
		{
			$instance->add_available_key($key);
		}
		return $instance;
	}
} 