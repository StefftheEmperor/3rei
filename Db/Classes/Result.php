<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:44
 */

namespace Db\Classes;

use Model\Classes\AbstractModel;
use Db\Classes\Filter\Comparison;
use Db\Classes\Expression\AbstractExpression;
use Db\Classes\Table\Column;

class Result extends AbstractModel {

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

		if ($reflection_class->implementsInterface('\Db\Interfaces\AbstractModel'))
		{
			$instance = $reflection_class->newInstance($this->get_statement()->get_connection());
		} else {
			$instance = $reflection_class->newInstance();
		}

		if ($this->get_query()->is_insert())
		{
			$instance->set_primary_id($this->get_statement()->get_connection()->lastInsertId());
		}

		foreach ($this->get_query()->get_filter() as $filter)
		{
			if ($filter instanceof Comparison)
			{
				$operand1 = $filter->get_operand(0);
				if ($operand1 instanceof AbstractExpression)
				{
					$operand1 = $operand1->get_filtered();
				}

				$operand2 = $filter->get_operand(1);
				if ($operand2 instanceof AbstractExpression)
				{
					$operand2 = $operand2->get_filtered();
				}

				if ($operand1 instanceof Column)
				{
					$operand1 = $operand1->get_field();
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

			if ($instance instanceof \Db\Classes\AbstractModel) {
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