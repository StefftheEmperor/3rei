<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 18:28
 */

namespace db\classes\mysql;

use Db\Classes\Result;

class Statement implements \Iterator, \Countable {

	/**
	 * @var \PDOStatement $pdo_statement
	 */
	protected $pdo_statement;

	protected $current = null;
	protected $key = null;

    protected $attributes = array();

	/**
	 * @var \Db\Classes\Mysql\Connection
	 */
	protected $connection;
	/**
	 * @var \Db\Classes\Mysql\Query
	 */
	protected $query;
	public function __construct($pdo_statement)
	{
		$this->pdo_statement = $pdo_statement;
	}

	public function set_connection($connection)
	{
		$this->connection = $connection;
	}

	/**
	 * @return Connection
	 */
	public function get_connection()
	{
		return $this->connection;
	}

	/**
	 * @return \Db\Mysql\Query
	 */
	public function get_query()
	{
		return $this->query;
	}

	/**
	 * @param Query $query
	 */
	public function set_query(Query $query)
	{
		$this->query = $query;
	}

	/**
	 *
	 */
	public function next()
	{

		$current_row = $this->pdo_statement->fetch(\PDO::FETCH_ASSOC);

		if ($current_row === FALSE) {
			$current = new \Db\Classes\Result($this);
		} else {
			$current = new \Db\Classes\Result($this);
			foreach ($current_row as $key => $value)
			{
				$property_name = $this->get_query()->get_property_name_by_alias($key);
				if ( ! isset($property_name))
				{
					$property_name = $key;
				}
				$current->{'set_'.strtolower($property_name)}($value);
			}
		}

		if ($this->get_query() instanceof \Db\Classes\Mysql\Query AND $this->get_query()->is_select() AND $this->get_query()->get_from() instanceof \Db\Table)
		{

			$table = $this->get_query()->get_from();

			foreach ($table->get_columns() as $column)
			{
				$current->add_available_key($column->get_field());
			}
		}

		$this->current = $current;

		($this->key === NULL ? ($this->key = 0) : ($this->key++));

	}

	public function valid()
	{
		$key = ($this->key === NULL ? 0 : $this->key);
		$row_count = $this->pdo_statement->rowCount();
		$valid = $key < $row_count;

		return $valid;
	}

	public function current()
	{
		if ($this->current === null) {
			$this->next();
		}

		return $this->current;
	}

	public function find($other, $strict = FALSE)
	{
		if ($strict)
		{
			foreach ($this as $row)
			{
				if ($row === $other) {
					return $row;
				}
			}
		}
		else
		{
			foreach ($this as $row)
			{
				if ($row === $other) {
					return $row;
				}
			}
		}
		return NULL;
	}
	public function map_to($class_name)
	{
		$result = new \Db\Classes\ResultSet;

		foreach ($this as $result_row)
		{
			$result->add($result_row->map_to($class_name));
		}
		return $result;
	}

	public function rewind() {
		$this->pdo_statement->closeCursor();
		$this->pdo_statement->execute($this->attributes);
		$this->key = NULL;
		$this->current = NULL;
	}

    public function count()
    {
        return $this->pdo_statement->rowCount();
    }

    public function key()
    {
        return $this->key;
    }

	public function lastInsertId()
	{
		return $this->get_connection()->lastInsertId();
	}
	/**
	 * @param $attributes
	 * @return $this
	 */
    public function execute($attributes = NULL)
    {
		if (isset($attributes)) {
			$this->attributes = array_merge($this->attributes, $attributes);
		}
		try {
			$this->pdo_statement->execute($this->attributes);
			if ($this->get_query()->is_insert())
			{
				$insert_id = $this->get_connection()->lastInsertId();
				$current = new Result($this);

				$current->{'set_'.$this->get_query()->get_table()->get_primary_key()}($insert_id);
				$this->current = $current;
			}
			$error_code = $this->pdo_statement->errorCode();
			$error_info = $this->pdo_statement->errorInfo();

			$error_message = 'Error performing db request';
			if (is_array($error_info) AND count($error_info) == 3)
			{
				$error_message = $error_info[2];
			}
			if ($error_code !== \PDO::ERR_NONE)
			{
				throw new \Db\Classes\Statement\Exception($error_message, intval($error_code));
			}
		}
		catch (\Exception $e)
		{

			if ($e instanceof \Db\Classes\Exception) {
				$e->set_history($this->get_connection()->get_history());
			}
			throw $e;
		}

		return $this;
    }
} 