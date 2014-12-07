<?php
/**
 * Created by PhpStorm.
 * User: shireen
 * Date: 10.09.14
 * Time: 17:48
 */

namespace db;


class Filter {

    protected $operand1;
    protected $operator;
    protected $operand2;
    public function __construct($operand1, $operator, $operand2)
    {
        $this->operand1 = $operand1;
        $this->operator = $operator;
        $this->operand2 = $operand2;
    }

	public function get_operand1()
	{
		return $this->operand1;
	}

	public function get_operand2()
	{
		return $this->operand2;
	}

	public function get_operator()
	{
		return $this->operator;
	}
} 