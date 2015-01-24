<?php
/**
 * Created by PhpStorm.
 * User: shireen
 * Date: 10.09.14
 * Time: 18:31
 */

namespace Db\Mysql;

class Query extends \Db\AbstractQuery {


	protected $parts = array();

	/**
	 * @var \Db\Mysql\Connection
	 */
	protected $connection;

	protected $type;

	protected $from;

	protected $filter = array();

	/**
	 * @param $connection
	 * @param $type
	 * @param $parts
	 */
	public function __construct($connection, $type, $parts = NULL)
	{
		$this->connection = $connection;
		$this->parts = $parts;
		$this->type = $type;
	}

	public function from($table) {
		if (is_string($table)) {
			$table = new \Db\Table($table);
		}

		$this->from = $table;

		return $this;
	}

	public function filter(\Db\Filter $filter)
	{
		$this->filter = array($filter);

		return $this;
	}

	public function set_filter($filter)
	{
		return $this->filter($filter);
	}

	/**
	 * @return Statement
	 */
	public function execute()
	{
		$result = $this->connection->query($this);

		return $result;
	}

	/**
	 * @param $connection
	 * @param $parts
	 * @return $this
	 */
	public static function select($connection, $parts)
	{
		return new static($connection, static::QUERY_SELECT, $parts);
	}

	public function is_select()
	{
		return $this->type === static::QUERY_SELECT;
	}

	public function is_describe()
	{
		return $this->type === static::QUERY_DESCRIBE;
	}

	public function is_insert()
	{
		return $this->type === static::QUERY_INSERT;
	}

	public function is_update()
	{
		return $this->type === static::QUERY_UPDATE;
	}
	
	public function get_table()
	{
		return $this->get_from();
	}

	public function set_table($table)
	{
		return $this->from($table);
	}

	public function get_parts()
	{
		return $this->parts;
	}

	public function get_filter_as_string()
	{
		$where = '';
		$filters = $this->get_filter();
		if (count($filters) > 0) {
			$filter_iterator = 0;
			foreach ($filters as $filter) {
				if ($filter_iterator == 0) {
					$where .= ' WHERE ';
				} elseif ($filter_iterator > 0) {
					$where .= ' AND ';
				}
				$where .= $filter->get_operand1() . ' ' . $filter->get_operator().' '.$filter->get_operand2();
			}
		}

		return $where;
	}
	public function get_filter()
	{
		return $this->filter;
	}

	public function get_type()
	{
		return $this->type;
	}

	public function get_from()
	{
		return $this->from;
	}

	public function set_from($from)
	{
		return $this->set_table($from);
	}


}