<?php
/**
 * Created by PhpStorm.
 * User: shireen
 * Date: 10.09.14
 * Time: 17:48
 */

namespace Db\Classes;


use Debug\Classes\CustomException;

abstract class Filter {

	protected $operands = NULL;

	public function __construct($operand1, $operand2, $_ = NULL)
	{
		$arguments = func_get_args();


		while (($operand = array_shift($arguments)) !== NULL)
		{
			if ( ! isset($this->operands))
			{
				$this->operands = array();
			}

			if ($operand instanceof \Db\Classes\Table\Column)
			{
				$operand = \Db\Classes\Expression\Row::factory($operand);
			}
			$this->operands[] = $operand;
		}
	}

	public static function factory($operand1 = NULL, $operand2 = NULL, $_ = NULL)
	{
		$arguments = func_get_args();
		$reflection = new \ReflectionClass(get_called_class());
		return $reflection->newInstanceArgs($arguments);
	}

	public function get_operand($num)
	{
		if (array_key_exists($num, $this->operands))
		{
			return $this->operands[$num];
		}
		else
		{
			return NULL;
		}
	}

	public function get_operands()
	{
		return $this->operands;
	}
} 