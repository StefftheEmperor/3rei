<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 10.06.15
 * Time: 10:52
 */

namespace Db\Classes\Table;


use Db\Classes\Exception;

class Select {

	protected $statement = NULL;
	protected $alias = NULL;

	public static function factory($statement = NULL, $alias = NULL)
	{
		if ( ! isset($statement))
		{
			throw new Exception('statement needs to be set');
		}

		if ( ! isset($alias))
		{
			throw new Exception('alias needs to be set');
		}

		$select = new static;
		$select->set_statement($statement);
		$select->set_alias($alias);

		return $select;
	}

	public function get_statement()
	{
		return $this->statement;
	}

	public function set_statement($statement)
	{
		$this->statement = $statement;

		return $this;
	}

	public function get_alias()
	{
		return $this->alias;
	}

	public function set_alias($alias)
	{
		$this->alias = $alias;

		return $this;
	}
}