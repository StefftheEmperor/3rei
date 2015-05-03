<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 17:55
 */

namespace Db\Classes\Mysql;

class Connection {

	protected $dbh = NULL;

	protected static $history = array();
	public function __construct($host, $port, $database, $username, $password)
	{
		$this->dbh = new \PDO('mysql:host='.$host.';port='.$port.';dbname='.$database, $username, $password);
	}

	/**
	 * @param $query
	 * @param array $attributes
	 * @return Statement
	 */
	public function query($query, $attributes = array())
	{

		if ($query instanceof \Db\Classes\Mysql\Query) {
			if ($query->is_select()) {
				$parts = $query->get_parts();
				$where = $query->get_filter_as_string();
				$query_string = $query->get_type().' '.((isset($parts) AND (count($parts) > 0)) ? ('`'.implode('`, `', $parts).'`') : '*').' FROM '.$query->get_from()->get_table_name().$where;
			}
			elseif ($query->is_describe())
			{

				$query_string = $query->get_type().' '.$query->get_from()->get_table_name();
			}
			elseif ($query->is_insert())
			{
				$parts = $this->sleeping_parts($query->get_parts());

				$keys = $values = '';
				foreach ($parts as $key => $value)
				{
					if ($keys !== '')
					{
						$keys .= ', ';
					}

					$keys .= '`'.$key.'`';

					if ($values !== '')
					{
						$values .= ', ';
					}

					$value_string = ':'.$key;
					$values .= $value_string;

					$attributes[$value_string] = $value;
				}
				$query_string = 'INSERT INTO '.$query->get_from()->get_table_name().' ('.$keys.') VALUES ('.$values.')';

			}
			elseif ($query->is_update())
			{
				$parts = $this->sleeping_parts($query->get_parts());

				$where = $query->get_filter_as_string();
				$key_value_pairs = '';
				foreach ($parts as $key => $value)
				{
					if ($key_value_pairs !== '')
					{
						$key_value_pairs .= ', ';
					}

					$key_value_pairs .= '`'.$key.'`';

					$value_string = ':'.$key;
					$key_value_pairs .= '='.$value_string;
					$attributes[$value_string] = $value;
				}
				$query_string = 'UPDATE '.$query->get_from()->get_table_name().' SET '.$key_value_pairs.$where;

			}
			else
			{
				throw new \Debug\Classes\CustomException('Undefined query-type: '.$query->get_type());
			}

		} else {
			throw new \Debug\Classes\CustomException('Query must be \Db\Query - '.gettype($query).' given');
		}

		static::$history[] = $query_string;
		$sth = new \Db\Classes\Mysql\Statement($this->dbh->prepare($query_string));
		$sth->set_connection($this);
		$sth->set_query($query);
		$sth->execute($attributes);

		return $sth;
	}

	/**
	 * @param null $parts
	 * @return  \Db\Mysql\Query
	 */
	public function select($parts = NULL)
	{
		return \Db\Classes\Mysql\Query::select($this, $parts);
	}

	public function describe_table($table_name)
	{

		$query = new Query($this, Query::QUERY_DESCRIBE);
		$query->set_table($table_name);
		$table = $query->get_table();
		$columns = $this->query($query)->execute();

		foreach ($columns as $column)
		{
			$column_object = $column->map_to('\Db\Classes\Column');

			if ($column_object->get_key() === 'PRI')
			{
				$table->set_primary_key($column_object->get_field());
			}
		}

		return $columns;
	}

	public function get_history()
	{
		return static::$history;
	}

	function sleeping_parts($query)
	{
		$sleeping_parts = array();
		if (method_exists($query, 'before_sleep'))
		{
			$query->before_sleep();
		}

		if (method_exists($query, 'sleep'))
		{
			$query->sleep();
		}

		foreach ($query as $key => $value)
		{
			if (method_exists($query, ('get_sleeping_'.$key)))
			{
				$sleeping_parts[$key] = $query->{'get_sleeping_'.$key}();
			} else {
				$sleeping_parts[$key] = $value;
			}
		}

		return $sleeping_parts;
	}
} 