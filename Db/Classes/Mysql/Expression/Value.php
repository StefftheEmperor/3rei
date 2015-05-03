<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 14.12.14
 * Time: 18:15
 */

namespace Db\Classes\Mysql\Expression;


class Value extends \Db\Classes\Expression\AbstractExpression {

	public function get_filtered()
	{
		if ($this->get_unfiltered() === NULL)
		{
			return 'NULL';
		} elseif (is_numeric($this->get_unfiltered()))
		{
			return $this->get_unfiltered();
		}
		else {
			return '"'.$this->get_unfiltered().'"';
		}
	}
}