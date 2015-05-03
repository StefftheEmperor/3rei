<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 17:07
 */

namespace Db\Classes;

abstract class AbstractModel extends \Model\Classes\AbstractModel {

	private $table = NULL;
	protected $table_name = NULL;
	protected $table_model = NULL;
	protected $primary_key = NULL;

	protected $is_new = TRUE;
	final public function __construct()
	{

		$this->init_table();
		if (($this->get_table() === NULL) OR ($this->get_table()->get_primary_key() === NULL))
		{
			throw new Exception('Primary id needs to be set in Class '.get_called_class());
		}
		$this->init();
	}

	protected function init()
	{

	}

	protected function get_table_model()
	{
		return $this->table_model;
	}

	protected function init_table_model()
	{
		$this->table_model = '\Db\Classes\Table';
	}

	protected function init_table()
	{
		$this->init_table_model();
		if (isset($this->table_name))
		{
			$table_model_reflection = new \ReflectionClass($this->get_table_model());
			$this->table = $table_model_reflection->newInstance($this->table_name);
			if (isset($this->primary_key))
			{
				$this->table->set_primary_key($this->primary_key);
			}
		}
	}

	public function set_table(\Db\Table $table)
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

	public function is_new($value = NULL)
	{
		if (isset($value))
		{
			$this->is_new = $value;

			return $this;
		} else {
			return $this->is_new;
		}
	}

	public function get_primary_key()
	{
		$primary_key = $this->get_table()->get_primary_key();

		return $this->__get($primary_key);
	}

	public function get_primary_id()
	{
		return $this->{'get_'.strtolower($this->get_primary_key())}();

	}
	
	public function set_primary_key($value)
	{
		$primary_key = $this->get_table()->get_primary_key();

		return $this->__set($primary_key, $value);
	}

	public function set_primary_id($value)
	{
		return $this->{'set_'.strtolower($this->get_primary_key())}($value);
	}

	public function save()
	{
		if ($this->is_new()) {
			$query = new \Db\Classes\Mysql\Query(\Model\Classes\Registry::get('database'), \Db\Classes\Mysql\Query::QUERY_INSERT, $this);
		} else {
			$filter = new Filter($this->get_table()->get_primary_key(), '=', $this->__get($this->get_table()->get_primary_key()));
			$query = new \Db\Classes\Mysql\Query(\Model\Classes\Registry::get('database'), \Db\Classes\Mysql\Query::QUERY_UPDATE, $this);
			$query->set_filter($filter);
		}

		$query->set_table($this->get_table());

		$result = $query->execute();

		return $result;
	}

	public function offsetGet($key)
	{

		if (array_key_exists($key, $this->get_table()->get_foreign_keys_map()))
		{
			$foreign_key = $this->get_table()->get_foreign_key($key);
			return call_user_func(array($foreign_key->get_foreign_model(), 'factory_by_'.$foreign_key->get_foreign_key()), parent::offsetGet($foreign_key->get_key()));
		}
		else
		{
			return parent::offsetGet($key);
		}
	}

	public final function before_sleep()
	{
		$this->get_table()->before_sleep();
	}
} 