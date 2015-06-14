<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 07.06.15
 * Time: 16:02
 */

namespace Db\Traits;


trait Model {

	protected $connection = NULL;
	private $table = NULL;
	protected $table_model = NULL;

	final public function __construct(\Db\Classes\Mysql\Connection $connection)
	{
		$this->connection = $connection;
		$this->init_table();
		if (($this->get_table() === NULL) OR ($this->get_table()->get_primary_key() === NULL))
		{
			throw new \Db\Classes\Exception('Primary id needs to be set in Class '.get_called_class());
		}
		$this->init();
	}

	public function get_connection()
	{
		return $this->connection;
	}

	protected function init_table()
	{
		$this->init_table_model();
		$table_name = $this->get_table_name();
		if (isset($table_name))
		{
			$table_model_reflection = new \ReflectionClass($this->get_table_model());
			$this->table = $table_model_reflection->newInstance($this->connection, $table_name);
			if (isset($this->primary_key))
			{
				$this->table->set_primary_key($this->primary_key);
			}
		}
	}

	protected function init_table_model()
	{
		$this->table_model = '\Db\Classes\Table';
	}

	public function set_table(\Db\Classes\Table $table)
	{
		foreach ($table->get_columns() as $column)
		{
			$this->add_available_key($column->get_field());
		}

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

	protected function get_table_model()
	{
		return $this->table_model;
	}

	public function get_column($column_name)
	{
		return $this->get_table()->get_column($column_name);
	}
}