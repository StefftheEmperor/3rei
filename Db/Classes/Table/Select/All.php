<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 10.06.15
 * Time: 12:16
 */

namespace Db\Classes\Table\Select;


class All extends \Db\Classes\Table\Select
{

	protected $table = NULL;

	public function __construct($statement = NULL)
	{
		$this->table = $statement;
	}
	public static function factory($statement = NULL, $alias = NULL)
	{
		return new static($statement);
	}

	public function get_table()
	{
		return $this->table;
	}
}