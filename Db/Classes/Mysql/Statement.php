<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 18:28
 */

namespace db\classes\mysql;

class Statement implements \Iterator, \Countable {

	protected $pdo_statement;

	protected $current = null;
	protected $key = null;

    protected $attributes = array();

	protected $connection;
	/**
	 * @var \Db\Mysql\Query
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

	public function get_connection()
	{
		return $this->connection;
	}
	public function get_query()
	{
		return $this->query;
	}

	public function set_query(\Db\Classes\Mysql\Query $query)
	{
		$this->query = $query;
	}
	public function next()
	{

		$current_row = $this->pdo_statement->fetch(\PDO::FETCH_ASSOC);

		if ($current_row === FALSE) {
			$current = new \Db\Classes\Result($this);
		} else {
			$current = new \Db\Classes\Result($this);
			foreach ($current_row as $key => $value)
			{
				$current->{'set_'.strtolower($key)}($value);
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
		}
		catch (\Exception $e)
		{

			$exception = new Exception($e->getMessage(), $e->getCode());
			$exception->set_history($this->get_connection()->get_history());
			throw $exception;
		}

		return $this;
    }
} 