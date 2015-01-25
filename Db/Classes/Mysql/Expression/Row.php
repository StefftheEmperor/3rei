<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 15:13
 */

namespace Db\Classes\Mysql\Expression;

class Row extends \Db\Classes\Expression\AbstractExpression {

	public function get_filtered()
	{
		return '`'.$this->get_unfiltered().'`';
	}
}