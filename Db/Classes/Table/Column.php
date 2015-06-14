<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 18:17
 */

namespace Db\Classes\Table;


class Column extends \Model\Classes\AbstractModel implements \Db\Interfaces\AbstractModel
{
	protected $is_primary;
	protected $connection = NULL;
	protected $table = NULL;
	protected $alias = NULL;

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	/**
	 * @return $this
	 */
	public function set_table($table)
	{
		$this->table = $table;

		return $this;
	}

	/**
	 * @return \Db\Classes\Table
	 */
	public function get_table()
	{
		return $this->table;
	}

	public function set_key($key)
	{
		if ($key === 'PRI')
		{
			$this->is_primary = TRUE;
		}
		else
		{
			$this->is_primary = FALSE;
		}
		parent::set_key($key);
	}

	protected function is_primary()
	{
		return $this->is_primary;
	}

	public function get_alias()
	{
		if ( ! isset($this->alias))
		{
			$this->alias = $this->get_table()->get_alias_for($this);
		}

		return $this->alias;
	}
} 