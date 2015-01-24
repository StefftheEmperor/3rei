<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 17:55
 */

namespace Db\Mysql;

use Db\Exception;
use Db\Table;
use Db\Mysql;

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

		if ($query instanceof \Db\Mysql\Query) {
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
				$parts = $query->get_parts();

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
			else
			{
				throw new Exception('Undefined query-type: '.$query->get_type());
			}

		} else {
			throw new Exception('Query must be \Db\Query - '.gettype($query).' given');
		}

		static::$history[] = $query_string;
		$sth = new Statement($this->dbh->prepare($query_string));
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
		return \Db\Mysql\Query::select($this, $parts);
	}

	public function describe_table($table_name)
	{

		$query = new Query($this, Query::QUERY_DESCRIBE);
		$query->set_table($table_name);
		$table = $query->get_table();
		$columns = $this->query($query)->execute();

		foreach ($columns as $column)
		{
			$table->add_column($column->map_to('\Db\Column'));
		}


		return $columns;
	}

	public function get_history()
	{
		return static::$history;
	}
} 