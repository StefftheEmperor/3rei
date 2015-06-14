<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 17:55
 */

namespace Db\Classes\Mysql;

class Connection extends \Db\Classes\AbstractConnection implements \Db\Interfaces\Connection {

	protected $dbh = NULL;

	protected static $history = array();

	protected $database = NULL;

	public function __construct($host, $port, $database, $username, $password)
	{
		$this->dbh = new \PDO('mysql:host='.$host.';port='.$port.';dbname='.$database, $username, $password);

		$this->database = $database;
	}

	public function get_database()
	{
		return $this->database;
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

				$select_string = $query->get_select_string($query->get_scope());
				$from_string = $query->get_from_string($query->get_scope());
				$join_scope = $query->get_scope()->get_new_child(array(\Db\Classes\Mysql\Scope::OPTION_INHERIT_TABLES));
				$join_string = $query->get_joins_as_string($join_scope);

				$where_scope = $query->get_scope()->get_new_child(array(\Db\Classes\Mysql\Scope::OPTION_INHERIT_TABLES));
				$where_scope->add_child($join_scope);
				$where_string = $query->get_where_as_string($where_scope);
				$group_string = $query->get_group_string($query->get_scope(array(\Db\Classes\Mysql\Scope::OPTION_INHERIT_TABLES)));
				$order_string = $query->get_order_string($query->get_scope());

				$query_string = $query->get_type().' '.$select_string.$from_string.$join_string.$where_string.$group_string.$order_string;
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
			$column_object = $column->map_to('\Db\Classes\Table\Column');
			$column_object->set_table($table);
			if ($column_object->get_key() === 'PRI')
			{
				$primary_field = $column_object->get_field();
				$table->set_primary_key($primary_field);
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

	/**
	 * @return string
	 */
	public function last_insert_id()
	{
		return $this->dbh->lastInsertId();
	}

	/**
	 * @return string
	 */
	public function lastInsertId()
	{
		return $this->last_insert_id();
	}

	public function get_last_query()
	{
		$history = $this->get_history();
		return end($history);
	}

} 