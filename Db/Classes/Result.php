<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:44
 */

namespace Db\Classes;

class Result extends \Model\Classes\AbstractModel {

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

	public function rewind()
	{

		parent::rewind();
	}


	public function next()
	{

		parent::next();
	}

	public function key()
	{

		return parent::key();
	}

	public function map_to($classname)
	{
		$reflection_class = new \ReflectionClass($classname);

		$instance = $reflection_class->newInstance();

		if ($this->get_query()->is_insert())
		{
			$instance->set_primary_id($this->get_statement()->get_conenction()->lastInsertId());
		}

		foreach ($this->get_query()->get_filter() as $filter)
		{
			if ($filter->get_operator() === '=') {
				$operand1 = $filter->get_operand1();
				if ($operand1 instanceof \Db\Classes\Expression\AbstractExpression)
				{
					$operand1 = $operand1->get_filtered();
				}

				$operand2 = $filter->get_operand2();
				if ($operand2 instanceof \Db\Classes\Expression\AbstractExpression)
				{
					$operand2 = $operand2->get_filtered();
				}

				$instance->{'set_' . strtolower($operand1)}($operand2);
			}
		}

		if ($instance instanceof \Db\Classes\AbstractModel)
		{
			foreach ($instance->get_table()->get_columns() as $column)
			{
				$instance->{'set_'.strtolower($column->get_field())}(NULL);

			}
		}
		foreach ($this as $key => $value)
		{
			$instance->{'set_'.strtolower($key)}($value);

			if ($instance instanceof \Db\AbstractModel) {
				if ($key == $instance->get_table()->get_primary_key()) {
					$instance->is_new(FALSE);
				}
			}
		}

		foreach ($this->get_available_keys() as $key)
		{
			$instance->add_available_key($key);
		}

		return $instance;
	}
} 